<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subMonths(6)->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->addMonths(1)->endOfDay();
        $division = $request->division;

        $taskQuery = Task::whereBetween('created_at', [$startDate, $endDate]);

        if ($division && $division !== 'Semua') {
            $taskQuery->where('division', $division);
        }

        $tasks = $taskQuery->with(['assignees', 'creator', 'submissions'])->latest()->get();

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'Disetujui')->count();
        $inProgressTasks = $tasks->where('status', 'Dikerjakan')->count();
        $reviewTasks = $tasks->where('status', 'Menunggu Review')->count();
        $revisionTasks = $tasks->where('status', 'Revisi')->count();

        // Team performance
        $teamMembers = User::where('role', 'karyawan')
            ->with(['tasks', 'submissions'])
            ->get()
            ->map(function ($member) use ($startDate, $endDate) {
                $assignedCount = $member->tasks()->whereBetween('tasks.created_at', [$startDate, $endDate])->count();
                $submittedCount = $member->submissions()->whereBetween('task_submissions.created_at', [$startDate, $endDate])->count();
                $approvedCount = $member->submissions()->where('task_submissions.status', 'Disetujui')->whereBetween('task_submissions.created_at', [$startDate, $endDate])->count();
                $revisionCount = $member->submissions()->where('task_submissions.status', 'Revisi')->whereBetween('task_submissions.created_at', [$startDate, $endDate])->count();

                return [
                    'user' => $member,
                    'assigned_count' => $assignedCount,
                    'submitted_count' => $submittedCount,
                    'approved_count' => $approvedCount,
                    'revision_count' => $revisionCount,
                    'completion_rate' => $assignedCount > 0 ? round(($approvedCount / $assignedCount) * 100) : 0,
                ];
            });

        return view('reports.index', compact(
            'tasks',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'reviewTasks',
            'revisionTasks',
            'teamMembers',
            'startDate',
            'endDate',
            'division'
        ));
    }

    public function exportCsv(Request $request)
    {
        $tasks = Task::with(['assignees', 'creator'])->latest()->get();

        $filename = "laporan_tugas_tim_kreatif_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://memory', 'w');

        // CSV Header
        fputcsv($handle, ['ID', 'Judul Tugas', 'Divisi', 'Prioritas', 'Status', 'Deadline', 'Ditugaskan Kepada', 'Dibuat Pada']);

        foreach ($tasks as $task) {
            $assignees = $task->assignees->pluck('name')->implode(', ');
            fputcsv($handle, [
                $task->id,
                $task->title,
                $task->division,
                $task->priority,
                $task->status,
                $task->deadline->format('Y-m-d H:i'),
                $assignees,
                $task->created_at->format('Y-m-d H:i'),
            ]);
        }

        fseek($handle, 0);

        return response()->stream(
            function () use ($handle) {
                fpassthru($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]
        );
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Query base depending on role
        if ($user->isSuperadmin()) {
            $taskQuery = Task::query();
        } else {
            // Karyawan sees tasks assigned to them or to their division
            $taskQuery = Task::whereHas('assignees', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })->orWhere('division', $user->division);
        }

        $totalTasks = (clone $taskQuery)->count();
        $inProgressTasks = (clone $taskQuery)->where('status', 'Dikerjakan')->count();
        $pendingReviewTasks = (clone $taskQuery)->where('status', 'Menunggu Review')->count();

        // Terlambat: Deadline lewat dan status belum Disetujui
        $overdueTasks = (clone $taskQuery)
            ->where('status', '!=', 'Disetujui')
            ->where('deadline', '<', Carbon::now())
            ->count();

        // Deadline Terdekat (Urutkan dari deadline terdekat)
        $nearestDeadlines = (clone $taskQuery)
            ->with(['assignees', 'creator'])
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        // Menunggu Review (Tasks with status Menunggu Review or recent submissions)
        $awaitingReviewTasks = Task::where('status', 'Menunggu Review')
            ->with(['assignees', 'submissions' => function ($q) {
                $q->latest();
            }])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        // Recent Submissions
        $recentSubmissions = TaskSubmission::with(['task', 'user'])
            ->latest()
            ->limit(4)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'totalTasks',
            'inProgressTasks',
            'pendingReviewTasks',
            'overdueTasks',
            'nearestDeadlines',
            'awaitingReviewTasks',
            'recentSubmissions'
        ));
    }
}


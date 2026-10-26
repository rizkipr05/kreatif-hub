<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskSubmission::with(['task', 'user', 'reviewer']);

        // Karyawan hanya melihat pengumpulan miliknya sendiri, Superadmin melihat seluruh tim
        if (!Auth::user()->isSuperadmin()) {
            $query->where('user_id', Auth::id());
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })->orWhereHas('task', function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%");
                })->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Division filter
        if ($request->filled('division') && $request->division !== 'Semua Divisi') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('division', $request->division);
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $query->where('status', $request->status);
        }

        $submissions = $query->latest()->paginate(15)->withQueryString();

        return view('submissions.index', compact('submissions'));
    }

    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
            'attachment' => 'nullable|file|max:20480', // 20MB
            'link' => 'nullable|url',
        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();
            $path = $file->storeAs('submissions', time() . '_' . $fileName, 'public');
            $filePath = $path;
        }

        // Check latest version for this task by this user
        $lastSubmission = TaskSubmission::where('task_id', $task->id)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        $version = $lastSubmission ? ($lastSubmission->version + 1) : 1;

        $submission = TaskSubmission::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'version' => $version,
            'notes' => $validated['notes'],
            'attachment' => $filePath ?? ($lastSubmission ? $lastSubmission->attachment : null),
            'link' => $validated['link'] ?? null,
            'status' => 'Menunggu Review',
        ]);

        // Update task status to 'Menunggu Review'
        $task->update(['status' => 'Menunggu Review']);

        // Notify superadmins
        $superadmins = User::where('role', 'superadmin')->get();
        foreach ($superadmins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'submission',
                'title' => 'Hasil kerja masuk',
                'message' => 'Submission baru untuk tugas: ' . $task->title . ' oleh ' . Auth::user()->name,
                'link' => route('submissions.index'),
            ]);
        }

        return redirect()->back()->with('success', 'Hasil kerja berhasil dikumpulkan! Menunggu review dari Superadmin.');
    }

    public function review(Request $request, TaskSubmission $submission)
    {
        // Only superadmin can review
        if (!Auth::user()->isSuperadmin()) {
            abort(403, 'Akses khusus Superadmin.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Disetujui,Revisi',
            'review_notes' => 'nullable|string',
        ]);

        $submission->update([
            'status' => $validated['status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Carbon::now(),
        ]);

        // Update parent task status as well
        $task = $submission->task;
        $task->update(['status' => $validated['status']]);

        // Notify the employee who submitted
        Notification::create([
            'user_id' => $submission->user_id,
            'type' => 'review',
            'title' => 'Hasil review',
            'message' => ($validated['status'] === 'Disetujui' ? 'Pekerjaan Anda disetujui: ' : 'Pekerjaan Anda memerlukan revisi: ') . $task->title,
            'link' => route('tasks.show', $task->id),
        ]);

        return redirect()->back()->with('success', 'Review hasil kerja berhasil disimpan: Status ' . $validated['status']);
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Task::with(['assignees', 'creator']);

        // Filtering for regular employee vs superadmin
        if (!$user->isSuperadmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('assignees', function ($sub) use ($user) {
                    $sub->where('users.id', $user->id);
                })->orWhere('division', $user->division);
            });
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->filled('priority') && $request->priority !== 'Semua Prioritas') {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->orderBy('deadline', 'asc')->paginate(15)->withQueryString();

        $allUsers = User::orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'allUsers'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isSuperadmin()) {
            abort(403, 'Akses terbatas untuk Superadmin.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'division' => 'required|string',
            'priority' => 'required|in:Rendah,Sedang,Tinggi',
            'deadline' => 'required|date',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
            'attachment' => 'nullable|file|max:10240', // 10MB
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('task_briefs', 'public');
        }

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'division' => $validated['division'],
            'priority' => $validated['priority'],
            'status' => 'Belum Dimulai',
            'deadline' => $validated['deadline'],
            'created_by' => Auth::id(),
            'attachment' => $attachmentPath,
        ]);

        $task->assignees()->attach($validated['assignees']);

        // Send notifications to assignees
        foreach ($validated['assignees'] as $userId) {
            if ($userId != Auth::id()) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'task',
                    'title' => 'Tugas baru',
                    'message' => 'Anda ditugaskan pada: ' . $task->title,
                    'link' => route('tasks.show', $task->id),
                ]);
            }
        }

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil dibuat dan ditugaskan ke anggota tim.');
    }

    public function show(Task $task)
    {
        $task->load(['assignees', 'creator', 'submissions.user', 'submissions.reviewer']);
        $allUsers = User::orderBy('name')->get();

        return view('tasks.show', compact('task', 'allUsers'));
    }

    public function update(Request $request, Task $task)
    {
        if (!Auth::user()->isSuperadmin()) {
            abort(403, 'Akses terbatas untuk Superadmin.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'division' => 'required|string',
            'priority' => 'required|in:Rendah,Sedang,Tinggi',
            'status' => 'required|in:Belum Dimulai,Dikerjakan,Menunggu Review,Disetujui,Revisi',
            'deadline' => 'required|date',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
        ]);

        if ($request->hasFile('attachment')) {
            if ($task->attachment) {
                Storage::disk('public')->delete($task->attachment);
            }
            $task->attachment = $request->file('attachment')->store('task_briefs', 'public');
        }

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'division' => $validated['division'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'deadline' => $validated['deadline'],
        ]);

        $task->assignees()->sync($validated['assignees']);

        return redirect()->back()->with('success', 'Tugas berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:Belum Dimulai,Dikerjakan,Menunggu Review,Disetujui,Revisi',
        ]);

        $task->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status tugas berhasil diubah menjadi ' . $validated['status']);
    }

    public function destroy(Task $task)
    {
        if (!Auth::user()->isSuperadmin()) {
            abort(403, 'Akses terbatas untuk Superadmin.');
        }

        if ($task->attachment) {
            Storage::disk('public')->delete($task->attachment);
        }
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil dihapus.');
    }
}


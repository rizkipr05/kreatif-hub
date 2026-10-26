<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class KreatifHubTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Kelola jobdesk tim');
        $response->assertSee('tanpa drama.');
        $response->assertSee('Kreatif Hub');
        $response->assertSee('Username');
    }

    public function test_user_can_login_with_username(): void
    {
        $response = $this->post('/login', [
            'username' => 'christoper',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_superadmin_can_access_dashboard_and_features(): void
    {
        $admin = User::where('role', 'superadmin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee($admin->name);
        $response->assertSee('TOTAL TUGAS');
        $response->assertSee('Deadline Terdekat');

        // Test Tasks Index
        $response = $this->actingAs($admin)->get('/tugas');
        $response->assertStatus(200);
        $response->assertSee('UPLOAD KONTEN HARIAN');

        // Test Submissions Index
        $response = $this->actingAs($admin)->get('/pengumpulan');
        $response->assertStatus(200);
        $response->assertSee('Pengumpulan Tim');

        // Test Notifications Index
        $response = $this->actingAs($admin)->get('/notifikasi');
        $response->assertStatus(200);
        $response->assertSee('Notifikasi');

        // Test Reports Index
        $response = $this->actingAs($admin)->get('/laporan');
        $response->assertStatus(200);
        $response->assertSee('Laporan');

        // Test User Management (Superadmin Only)
        $response = $this->actingAs($admin)->get('/manajemen-user');
        $response->assertStatus(200);
        $response->assertSee('Manajemen User');
    }

    public function test_karyawan_cannot_access_user_management_and_reports(): void
    {
        $karyawan = User::where('role', 'karyawan')->first();
        $this->assertNotNull($karyawan);

        // Karyawan cannot access user management
        $response = $this->actingAs($karyawan)->get('/manajemen-user');
        $response->assertStatus(403);

        // Karyawan cannot access company reports
        $response = $this->actingAs($karyawan)->get('/laporan');
        $response->assertStatus(403);
    }

    public function test_superadmin_can_create_new_task(): void
    {
        $admin = User::where('role', 'superadmin')->first();
        $karyawan = User::where('role', 'karyawan')->first();

        $response = $this->actingAs($admin)->post('/tugas', [
            'title' => 'Tugas Uji Otomatis Baru',
            'description' => 'Instruksi tugas otomatis',
            'division' => 'Video',
            'priority' => 'Tinggi',
            'deadline' => Carbon::now()->addDays(5)->toDateTimeString(),
            'assignees' => [$karyawan->id],
        ]);

        $response->assertRedirect('/tugas');
        $this->assertDatabaseHas('tasks', [
            'title' => 'Tugas Uji Otomatis Baru',
            'division' => 'Video',
        ]);
    }

    public function test_employee_can_submit_work_and_superadmin_can_review(): void
    {
        $admin = User::where('role', 'superadmin')->first();
        $karyawan = User::where('role', 'karyawan')->first();
        $task = Task::first();

        // Employee submits work
        $response = $this->actingAs($karyawan)->post("/tugas/{$task->id}/kumpul", [
            'notes' => 'Pekerjaan selesai, silakan dicek kak',
            'link' => 'https://instagram.com/p/test123',
        ]);

        $response->assertSessionHas('success');
        $submission = TaskSubmission::where('task_id', $task->id)->where('user_id', $karyawan->id)->latest()->first();
        $this->assertNotNull($submission);

        // Superadmin reviews work
        $response = $this->actingAs($admin)->patch("/pengumpulan/{$submission->id}/review", [
            'status' => 'Disetujui',
            'review_notes' => 'Sangat bagus, disetujui!',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('task_submissions', [
            'id' => $submission->id,
            'status' => 'Disetujui',
        ]);
    }

    public function test_user_can_upload_and_change_profile_photo(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::first();
        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg', 300, 300);

        $response = $this->actingAs($user)->put('/profil', [
            'name' => $user->name,
            'username' => $user->username,
            'avatar' => $file,
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertNotNull($user->avatar);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->avatar);
    }
}

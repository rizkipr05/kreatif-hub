<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Superadmin (Christoper Ade Immanuel - matching screenshot)
        $christoper = User::create([
            'name' => 'Christoper Ade Immanuel',
            'username' => 'christoper',
            'email' => 'cacc471d6y0449@student.devacademy.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'division' => 'Manajemen',
            'phone' => '081234567890',
        ]);

        // Second Superadmin (Owner/Bos)
        $owner = User::create([
            'name' => 'Owner Semesta Kreatif',
            'username' => 'owner',
            'email' => 'owner@kreatifhub.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'division' => 'Manajemen',
            'phone' => '081298765432',
        ]);

        // 2. Karyawan Tim Kreatif
        $rizka = User::create([
            'name' => 'Rizka Aulia Putri',
            'username' => 'rizka',
            'email' => 'rizka@kreatifhub.id',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'division' => 'Video',
            'phone' => '082111222333',
        ]);

        $nailul = User::create([
            'name' => 'Nailul Akhla Alfa',
            'username' => 'nailul',
            'email' => 'nailul@kreatifhub.id',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'division' => 'Desain',
            'phone' => '082144556677',
        ]);

        $adelia = User::create([
            'name' => 'Adelia Nabila',
            'username' => 'adelia',
            'email' => 'adelia@kreatifhub.id',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'division' => 'Foto',
            'phone' => '082188990011',
        ]);

        $rian = User::create([
            'name' => 'Rian Rizki',
            'username' => 'rian',
            'email' => 'rian@kreatifhub.id',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'division' => 'Copywriting',
            'phone' => '082122334455',
        ]);

        $zahra = User::create([
            'name' => 'Zahra Aulia',
            'username' => 'zahra',
            'email' => 'zahra@kreatifhub.id',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'division' => 'Desain',
            'phone' => '082166778899',
        ]);

        // 3. Create Tasks
        $task1 = Task::create([
            'title' => 'UPLOAD KONTEN HARIAN',
            'description' => 'Upload reels & feed daily routine studio, cantumkan tagar utama dan tag vendor.',
            'division' => 'Semua',
            'priority' => 'Sedang',
            'status' => 'Disetujui',
            'deadline' => Carbon::parse('2026-06-23 21:00:00'),
            'created_by' => $christoper->id,
        ]);
        $task1->assignees()->attach([$adelia->id, $christoper->id, $nailul->id]);

        $task2 = Task::create([
            'title' => 'Desain pintu kaca',
            'description' => 'Layout sticker sandblast untuk pintu masuk kantor baru, ukuran 200x90 cm.',
            'division' => 'Desain',
            'priority' => 'Sedang',
            'status' => 'Disetujui',
            'deadline' => Carbon::parse('2026-06-19 17:00:00'),
            'created_by' => $christoper->id,
        ]);
        $task2->assignees()->attach([$zahra->id]);

        $task3 = Task::create([
            'title' => 'UPLOAD KONTEN HARIAN',
            'description' => 'Konten tiktok trend perkenalan tim kreatif & teaser project semesta.',
            'division' => 'Video',
            'priority' => 'Sedang',
            'status' => 'Menunggu Review',
            'deadline' => Carbon::parse('2026-06-16 21:00:00'),
            'created_by' => $christoper->id,
        ]);
        $task3->assignees()->attach([$adelia->id, $christoper->id, $nailul->id]);

        $task4 = Task::create([
            'title' => 'UPLOAD CAMPAIGN VIDEO',
            'description' => 'Final cut video campaign 60s untuk launching brand partner semesta.',
            'division' => 'Video',
            'priority' => 'Tinggi',
            'status' => 'Disetujui',
            'deadline' => Carbon::parse('2026-06-16 23:59:00'),
            'created_by' => $christoper->id,
        ]);
        $task4->assignees()->attach([$rian->id, $rizka->id]);

        $task5 = Task::create([
            'title' => 'E-Greeting Letter Tahun Baru Islam 1 Muharram 1448 H',
            'description' => 'Bikin banner digital & format PDF e-greeting formal untuk email blast klien.',
            'division' => 'Desain',
            'priority' => 'Tinggi',
            'status' => 'Disetujui',
            'deadline' => Carbon::parse('2026-06-16 18:00:00'),
            'created_by' => $christoper->id,
        ]);
        $task5->assignees()->attach([$zahra->id]);

        $task6 = Task::create([
            'title' => 'UPLOAD KONTEN HARIAN',
            'description' => 'Fotografi catalog produk souvenir cetak outdoor.',
            'division' => 'Foto',
            'priority' => 'Sedang',
            'status' => 'Disetujui',
            'deadline' => Carbon::parse('2026-06-15 21:00:00'),
            'created_by' => $christoper->id,
        ]);
        $task6->assignees()->attach([$adelia->id, $christoper->id, $nailul->id]);

        $task7 = Task::create([
            'title' => 'Video Reels Branding Produk',
            'description' => 'Shooting & editing b-roll produk mockup kemasan ramah lingkungan.',
            'division' => 'Video',
            'priority' => 'Sedang',
            'status' => 'Dikerjakan',
            'deadline' => Carbon::now()->addDays(2),
            'created_by' => $christoper->id,
        ]);
        $task7->assignees()->attach([$rizka->id]);

        $task8 = Task::create([
            'title' => 'Copywriting Promo Flash Sale',
            'description' => 'Susun 3 variasi copywriting caption & broadcast WA promo akhir bulan.',
            'division' => 'Copywriting',
            'priority' => 'Tinggi',
            'status' => 'Revisi',
            'deadline' => Carbon::now()->addDay(),
            'created_by' => $christoper->id,
        ]);
        $task8->assignees()->attach([$rian->id]);

        // 4. Submissions (Pengumpulan Tim)
        TaskSubmission::create([
            'task_id' => $task1->id,
            'user_id' => $rizka->id,
            'version' => 2,
            'notes' => 'done @souvenirtahsemesta',
            'attachment' => 'image (15).png',
            'link' => 'https://instagram.com/souvenirtahsemesta',
            'status' => 'Disetujui',
            'review_notes' => 'Keren hasilnya, transisi sudah pas!',
            'reviewed_by' => $christoper->id,
            'reviewed_at' => Carbon::parse('2026-06-23 21:10:00'),
            'created_at' => Carbon::parse('2026-06-23 20:53:00'),
        ]);

        TaskSubmission::create([
            'task_id' => $task1->id,
            'user_id' => $christoper->id,
            'version' => 2,
            'notes' => 'done @semesta.digitalprinting',
            'attachment' => 'image (14).png',
            'link' => 'https://instagram.com/semesta.digitalprinting',
            'status' => 'Disetujui',
            'review_notes' => 'Feed grid sudah sinkron dengan jadwal promosi.',
            'reviewed_by' => $owner->id,
            'reviewed_at' => Carbon::parse('2026-06-23 21:15:00'),
            'created_at' => Carbon::parse('2026-06-23 20:51:00'),
        ]);

        TaskSubmission::create([
            'task_id' => $task3->id,
            'user_id' => $nailul->id,
            'version' => 1,
            'notes' => 'done @semesta.kreatifharian, mohon dicek kak sebelum di-boost',
            'attachment' => 'image (12).png',
            'link' => null,
            'status' => 'Menunggu Review',
            'created_at' => Carbon::parse('2026-06-16 20:10:00'),
        ]);

        TaskSubmission::create([
            'task_id' => $task8->id,
            'user_id' => $rian->id,
            'version' => 1,
            'notes' => 'Draft promo flash sale sudah dibuat kak.',
            'attachment' => 'copywriting_promo_v1.docx',
            'link' => null,
            'status' => 'Revisi',
            'review_notes' => 'Tolong tambahkan kalimat CTA menuju link bio dan batasan kuota promo.',
            'reviewed_by' => $christoper->id,
            'reviewed_at' => Carbon::now()->subHours(2),
            'created_at' => Carbon::now()->subHours(5),
        ]);

        // 5. Notifications
        Notification::create([
            'user_id' => $christoper->id,
            'type' => 'review',
            'title' => 'Hasil review',
            'message' => 'Pekerjaan Anda disetujui: UPLOAD KONTEN HARIAN',
            'link' => '/pengumpulan',
            'is_read' => false,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        Notification::create([
            'user_id' => $christoper->id,
            'type' => 'review',
            'title' => 'Hasil review',
            'message' => 'Pekerjaan Anda disetujui: Desain pintu kaca',
            'link' => '/pengumpulan',
            'is_read' => false,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        Notification::create([
            'user_id' => $christoper->id,
            'type' => 'submission',
            'title' => 'Hasil kerja masuk',
            'message' => 'Submission baru untuk tugas: UPLOAD KONTEN HARIAN oleh Nailul Akhla Alfa',
            'link' => '/pengumpulan',
            'is_read' => false,
            'created_at' => Carbon::now()->subHours(4),
        ]);

        Notification::create([
            'user_id' => $christoper->id,
            'type' => 'task',
            'title' => 'Tugas baru',
            'message' => 'Tugas baru telah dibuat: Video Reels Branding Produk',
            'link' => '/tugas',
            'is_read' => false,
            'created_at' => Carbon::now()->subHours(8),
        ]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tasks Table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('division')->default('Semua'); // Video, Desain, Foto, Copywriting, Manajemen, Semua
            $table->enum('priority', ['Rendah', 'Sedang', 'Tinggi'])->default('Sedang');
            $table->enum('status', ['Belum Dimulai', 'Dikerjakan', 'Menunggu Review', 'Disetujui', 'Revisi'])->default('Belum Dimulai');
            $table->dateTime('deadline');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // Task User Pivot (Assignees)
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Task Submissions (Pengumpulan Tim)
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('version')->default(1);
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->string('link')->nullable();
            $table->enum('status', ['Menunggu Review', 'Disetujui', 'Revisi'])->default('Menunggu Review');
            $table->text('review_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type')->default('info'); // review, submission, task
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('task_submissions');
        Schema::dropIfExists('task_user');
        Schema::dropIfExists('tasks');
    }
};


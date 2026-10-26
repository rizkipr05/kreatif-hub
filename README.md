# Kreatif Hub - Sistem Informasi Manajemen Tugas untuk Tim Kreatif

Platform kolaborasi tim kreatif berbasis web untuk mengelola jobdesk, pembagian tugas oleh Superadmin, pengumpulan hasil kerja oleh anggota tim kreatif, review & persetujuan tugas, hingga laporan performa tim.

---

## 🚀 Fitur Utama

1. **Autentikasi & Multi-Role (Superadmin / Owner & Karyawan)**
   - **Superadmin (Owner/Leader)**: Dapat membuat tugas, membagi jobdesk ke anggota tim, mereview pengumpulan (Disetujui / Minta Revisi), mengelola user tim kreatif, dan mencetak laporan.
   - **Karyawan**: Dapat melihat tugas yang ditugaskan kepadanya, mengubah status pengerjaan, mengunggah bukti/file hasil kerja, menyertakan link eksternal (Canva/GDrive/Instagram), dan menerima notifikasi hasil review.
   - **Fitur Quick Login Demo**: Tersedia tombol login 1-klik untuk akun Superadmin dan Karyawan di halaman login.

2. **Dashboard Interaktif**
   - 4 Kartu Statistik Real-time: **Total Tugas**, **Dikerjakan**, **Menunggu Review**, dan **Terlambat**.
   - Widget **Deadline Terdekat** dengan indikator badge prioritas dan sisa waktu pengerjaan.
   - Widget **Menunggu Review** untuk memantau tugas yang baru saja dikumpulkan oleh anggota tim.

3. **Manajemen Tugas (Jobdesk)**
   - Daftar seluruh tugas dengan filter berdasarkan **Status** (*Belum Dimulai, Dikerjakan, Menunggu Review, Disetujui, Revisi*) dan **Prioritas** (*Rendah, Sedang, Tinggi*).
   - Modal Tambah & Edit Tugas dengan dukungan multi-assignee (satu tugas dapat ditugaskan ke beberapa anggota sekaligus) dan upload file brief.
   - Avatar inisial dinamis untuk setiap anggota yang ditugaskan.

4. **Pengumpulan Tim (Submissions)**
   - Tampilan feed pengumpulan hasil kerja yang dikelompokkan per tugas dan versi pengerjaan.
   - Filter berdasarkan Divisi (*Video, Desain, Foto, Copywriting, Manajemen*).
   - Menampilkan catatan pengerjaan, pratinjau tautan lampiran file, dan link eksternal.
   - Modal Review langsung bagi Superadmin untuk memberikan persetujuan atau instruksi revisi.

5. **Notifikasi**
   - Notifikasi real-time untuk penugasan baru, hasil kerja yang masuk, dan feedback hasil review.
   - Tombol **Tandai Semua Dibaca** dan direct link ke halaman terkait.

6. **Laporan & Rekapitulasi Kinerja**
   - Filter laporan berdasarkan rentang tanggal dan divisi tim.
   - Statistik KPI: Tingkat penyelesaian, rata-rata tugas selesai vs revisi.
   - Tabel performa individu masing-masing anggota tim dengan progress bar persentase penyelesaian.
   - Ekspor data ke format **CSV / Excel** dan fungsi **Cetak / Print**.

7. **Manajemen User (Khusus Superadmin)**
   - Tambah anggota tim baru dengan email, password awal, role, dan divisi.
   - Edit profil user dan reset kata sandi akun karyawan.

---

## 🔑 Akun Bawaan (Default Login Credentials)

Masuk cukup menggunakan **Username** dan **Password**:

| Role | Nama | **Username** | Password | Divisi |
| :--- | :--- | :--- | :--- | :--- |
| **Superadmin (1)** | Christoper Ade Immanuel | **`christoper`** | `password` | Manajemen |
| **Superadmin (2)** | Owner Semesta Kreatif | **`owner`** | `password` | Manajemen |
| **Karyawan** | Rizka Aulia Putri | **`rizka`** | `password` | Video |
| **Karyawan** | Nailul Akhla Alfa | **`nailul`** | `password` | Desain |
| **Karyawan** | Adelia Nabila | **`adelia`** | `password` | Foto |
| **Karyawan** | Rian Rizki | **`rian`** | `password` | Copywriting |
| **Karyawan** | Zahra Aulia | **`zahra`** | `password` | Desain |

*(Semua akun default menggunakan password: `password`. Anda juga dapat mengklik tombol login cepat 1-klik di halaman login).*

---

## 🛠️ Cara Menjalankan Aplikasi di Lokal

### 1. Pastikan Service MySQL Aktif
Database MySQL sudah dibuat dengan nama `kreatif_hub_db` pada XAMPP:
```bash
/opt/lampp/bin/mysql -u root -e "SHOW DATABASES;"
```

### 2. Jalankan Laravel Server
Masuk ke direktori `/opt/lampp/htdocs/new` dan jalankan:
```bash
cd /opt/lampp/htdocs/new
php artisan serve
```

Aplikasi akan berjalan di:
👉 **`http://localhost:8000`** atau **`http://127.0.0.1:8000`**

### 3. Migrasi & Seeder Ulang (Jika Dibutuhkan)
Jika ingin me-reset database kembali ke data awal:
```bash
php artisan migrate:fresh --seed
```

---

## 🧪 Menjalankan Pengujian Otomatis (Unit/Feature Test)
Semua fungsi utama telah diuji dengan PHPUnit test suite:
```bash
php artisan test --filter=KreatifHubTest
```
Hasil: **5 passed (28 assertions)**.

<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationHelper
{
    /**
     * Kirim notifikasi ke user tertentu
     */
    public static function send($userId, $type, $title, $message, $link = null, $data = [])
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'data' => $data,
            'is_read' => false
        ]);
    }

    /**
     * Kirim notifikasi ke semua admin
     */
    public static function sendToAdmins($type, $title, $message, $link = null, $data = [])
    {
        $admins = User::whereHas('role', function ($q) {
            $q->where('role', 'admin');
        })->get();

        foreach ($admins as $admin) {
            self::send($admin->id, $type, $title, $message, $link, $data);
        }
    }

    /**
     * Kirim notifikasi ke reviewer tertentu
     */
    public static function sendToReviewer($reviewerId, $title, $message, $link = null)
    {
        return self::send($reviewerId, 'review', $title, $message, $link);
    }

    /**
     * Dapatkan jumlah notifikasi belum dibaca
     */
    public static function getUnreadCount($userId = null)
    {
        $userId = $userId ?? Auth::id();
        return Notification::where('user_id', $userId)->where('is_read', false)->count();
    }

    /**
     * Dapatkan notifikasi terbaru
     */
    public static function getLatest($userId = null, $limit = 10)
    {
        $userId = $userId ?? Auth::id();
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Notifikasi untuk pendaftaran skripsi baru (ke mahasiswa)
     */
    public static function skripsiRegistered($mahasiswaId, $pendaftaranId)
    {
        $mahasiswa = \App\Models\Mahasiswa::find($mahasiswaId);

        self::send(
            $mahasiswa->user_id,
            'pendaftaran',
            'Pendaftaran Skripsi Berhasil',
            'Pendaftaran sidang skripsi Anda telah dikirim dan sedang menunggu review.',
            route('mahasiswa.show-skripsi', $pendaftaranId),
            ['status' => 'pending', 'type' => 'skripsi']
        );
    }

    /**
     * Notifikasi untuk pendaftaran metodologi baru (ke mahasiswa)
     */
    public static function metodologiRegistered($mahasiswaId, $pendaftaranId)
    {
        $mahasiswa = \App\Models\Mahasiswa::find($mahasiswaId);

        self::send(
            $mahasiswa->user_id,
            'pendaftaran',
            'Pendaftaran Metodologi Berhasil',
            'Pendaftaran ujian metodologi Anda telah dikirim dan sedang menunggu review.',
            route('mahasiswa.show-metodologi', $pendaftaranId),
            ['status' => 'pending', 'type' => 'metodologi']
        );
    }

    /**
     * Notifikasi untuk admin (pendaftaran baru)
     */
    public static function newRegistrationToAdmin($mahasiswa, $jenis, $pendaftaranId)
    {
        $title = $jenis == 'skripsi' ? 'Pendaftaran Skripsi Baru' : 'Pendaftaran Metodologi Baru';
        $message = "Mahasiswa {$mahasiswa->user->name} ({$mahasiswa->npm}) telah mendaftar {$jenis}.";
        $route = $jenis == 'skripsi'
            ? route('admin.pendaftaran.skripsi.show', $pendaftaranId)
            : route('admin.pendaftaran.metodologi.show', $pendaftaranId);

        self::sendToAdmins($jenis, $title, $message, $route, [
            'npm' => $mahasiswa->npm,
            'nama' => $mahasiswa->user->name,
            'jenis' => $jenis
        ]);
    }

    /**
     * Notifikasi status review ke mahasiswa
     */
    public static function reviewStatusToMahasiswa($mahasiswaId, $jenis, $pendaftaranId, $status, $reviewerNotes = null)
    {
        $mahasiswa = \App\Models\Mahasiswa::find($mahasiswaId);

        $statusText = [
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision' => 'Perlu Revisi'
        ];

        $title = $jenis == 'skripsi' ? 'Status Sidang Skripsi' : 'Status Ujian Metodologi';
        $message = "Pendaftaran {$jenis} Anda {$statusText[$status]}.";

        if ($reviewerNotes) {
            $message .= " Catatan: {$reviewerNotes}";
        }

        $route = $jenis == 'skripsi'
            ? route('mahasiswa.show-skripsi', $pendaftaranId)
            : route('mahasiswa.show-metodologi', $pendaftaranId);

        self::send(
            $mahasiswa->user_id,
            'status',
            $title,
            $message,
            $route,
            ['status' => $status, 'jenis' => $jenis]
        );
    }

    /**
     * Notifikasi jadwal ke mahasiswa
     */
    public static function jadwalToMahasiswa($mahasiswaId, $jenis, $pendaftaranId, $tanggal, $waktu, $ruang)
    {
        $mahasiswa = \App\Models\Mahasiswa::find($mahasiswaId);

        $title = $jenis == 'skripsi' ? 'Jadwal Sidang Skripsi' : 'Jadwal Ujian Metodologi';
        $message = "Jadwal {$jenis} Anda telah ditentukan pada {$tanggal} pukul {$waktu} di ruang {$ruang}.";

        $route = $jenis == 'skripsi'
            ? route('mahasiswa.show-skripsi', $pendaftaranId)
            : route('mahasiswa.show-metodologi', $pendaftaranId);

        self::send(
            $mahasiswa->user_id,
            'jadwal',
            $title,
            $message,
            $route,
            ['tanggal' => $tanggal, 'waktu' => $waktu, 'ruang' => $ruang]
        );
    }
}

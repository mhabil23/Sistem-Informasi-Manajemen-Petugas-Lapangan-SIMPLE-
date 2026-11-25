<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\TrainingEvent;
use App\Models\TrainingParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicAttendanceController extends Controller
{
    /**
     * Tampilkan formulir presensi dan tanda tangan digital untuk peserta kegiatan.
     */
    public function show(Request $request, string $token)
    {
        $training = TrainingEvent::with(['participants' => function ($q) {
            $q->orderBy('sort_order')->orderBy('name');
        }, 'assignment'])->where('public_token', $token)->firstOrFail();

        // Cek jika perangkat ini sudah pernah absen pada kegiatan ini
        $cookieKey = 'signed_dh_' . $token;
        if ($signedId = $request->cookie($cookieKey)) {
            $signedParticipant = $training->participants()->find($signedId);
            if ($signedParticipant) {
                return redirect()->route('attendance.public.success', [
                    $token,
                    'p' => $signedParticipant->id,
                    'already' => 1,
                ]);
            }
        }

        $participants = $training->participants;
        $totalCount = $participants->count();
        $signedCount = $participants->filter(fn ($p) => !empty($p->signature_path))->count();
        $pendingCount = $totalCount - $signedCount;

        return view('public.attendance.show', compact(
            'training',
            'participants',
            'totalCount',
            'signedCount',
            'pendingCount'
        ));
    }

    /**
     * Proses penyimpanan tanda tangan digital peserta kegiatan.
     */
    public function submit(Request $request, string $token)
    {
        $training = TrainingEvent::where('public_token', $token)->firstOrFail();

        // 1. Cek proteksi perangkat: 1 perangkat hanya bisa absen 1 kali
        $cookieKey = 'signed_dh_' . $token;
        if ($signedId = $request->cookie($cookieKey)) {
            $prevParticipant = $training->participants()->find($signedId);
            $prevName = $prevParticipant ? $prevParticipant->name : 'sebelumnya';

            return back()->withErrors([
                'error' => "Perangkat ini sudah digunakan untuk melakukan presensi atas nama {$prevName}. Setiap peserta hanya dapat absen 1 kali dan tidak dapat mengabsenkan orang lain.",
            ]);
        }

        $validated = $request->validate([
            'participant_id' => ['required', 'integer'],
            'signature_data' => ['required', 'string'],
        ], [
            'participant_id.required' => 'Pilih nama Anda dari daftar peserta.',
            'signature_data.required' => 'Bubuhkan tanda tangan Anda pada kotak kanvas yang tersedia.',
        ]);

        $participant = $training->participants()
            ->where('id', $validated['participant_id'])
            ->firstOrFail();

        // 2. Cek proteksi peserta: jika nama peserta sudah pernah bertanda tangan, tolak
        if (!empty($participant->signature_path)) {
            return back()->withErrors([
                'participant_id' => "Peserta {$participant->name} sudah melakukan presensi sebelumnya dan tidak dapat mengisi lagi.",
            ]);
        }

        // Simpan tanda tangan digital
        $sigPath = $this->saveSignatureData($validated['signature_data'], $participant->id);

        $participant->update([
            'signature_path' => $sigPath,
            'signed_at' => now(),
            'attendance_status' => 'present',
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log(
                'attendance_signed',
                'training_participants',
                "Peserta {$participant->name} melakukan tanda tangan presensi online pada kegiatan {$training->title}."
            );
        }

        // Simpan cookie selama 30 hari agar perangkat terkunci tidak bisa absen lagi / untuk orang lain
        $cookie = cookie($cookieKey, (string) $participant->id, 60 * 24 * 30);

        return redirect()->route('attendance.public.success', [$token, 'p' => $participant->id])
            ->withCookie($cookie)
            ->with('success', "Terima kasih, {$participant->name}! Tanda tangan dan presensi Anda berhasil tercatat.");
    }

    /**
     * Halaman konfirmasi sukses presensi peserta.
     */
    public function success(Request $request, string $token)
    {
        $training = TrainingEvent::where('public_token', $token)->firstOrFail();
        $participant = null;

        if ($pId = $request->input('p')) {
            $participant = $training->participants()->find($pId);
        }

        return view('public.attendance.success', compact('training', 'participant'));
    }

    /**
     * Simpan file base64 tanda tangan digital dengan pembersihan transparan.
     */
    protected function saveSignatureData(string $sigData, int $participantId): ?string
    {
        if (empty($sigData) || !str_starts_with($sigData, 'data:image')) {
            return null;
        }

        try {
            $parts = explode(',', $sigData);
            if (count($parts) < 2) {
                return null;
            }

            $imgData = base64_decode($parts[1]);
            if ($imgData === false) {
                return null;
            }

            $dir = 'signatures';
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }

            $filename = "{$dir}/part_dh_{$participantId}_" . time() . '.png';
            Storage::disk('public')->put($filename, $imgData);

            return $filename;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

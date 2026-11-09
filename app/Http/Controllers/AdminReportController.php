<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ReportStatusHistory;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    // Memeriksa apakah pengguna memiliki akses sebagai admin/petugas
    private function authorizeStaff(Request $request)
    {
        abort_unless(
            in_array($request->user()->role, ['admin', 'petugas']),
            403,
            'Kamu tidak memiliki akses ke halaman ini.'
        );
    }

    // Menampilkan seluruh laporan warga
    public function index(Request $request)
    {
        $this->authorizeStaff($request);

        $reports = Report::with('user:id,name,email')
            ->latest()
            ->paginate(10);

        return response()->json($reports);
    }

    // Melihat detail laporan
    public function show(Request $request, $id)
    {
        $this->authorizeStaff($request);

    $report = Report::with([
        'user:id,name,email',
        'statusHistories.user:id,name',
    ])->findOrFail($id);

        return response()->json([
            'message' => 'Detail laporan berhasil diambil.',
            'data' => $report,
        ]);
    }

    // Memperbarui status laporan
    public function updateStatus(Request $request, $id)
    {
        $this->authorizeStaff($request);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'Menunggu',
                    'Diproses',
                    'Selesai',
                ]),
            ],
            'note' => 'nullable|string|max:1000',
        ]);

        $report = Report::findOrFail($id);

        if ($report->status === $validated['status']) {
            return response()->json([
                'message' => 'Status laporan tidak mengalami perubahan.',
                'data' => $report,
            ]);
        }

        DB::transaction(function () use ($report, $validated, $request) {
            $report->update([
                'status' => $validated['status'],
            ]);

            ReportStatusHistory::create([
                'report_id' => $report->id,
                'user_id' => $request->user()->id,
                'status' => $validated['status'],
                'note' => $validated['note'] ?? null,
            ]);
        });

        return response()->json([
            'message' => 'Status laporan berhasil diperbarui.',
            'data' => $report->fresh(),
        ]);
    }
}

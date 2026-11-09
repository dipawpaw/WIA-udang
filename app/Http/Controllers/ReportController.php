<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Report;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => [
                'required',
                Rule::in([
                    'Infrastruktur',
                    'Penerangan',
                    'Transportasi',
                    'Kebersihan',
                    'Air & Drainase',
                    'Lainnya',
                ]),
            ],

            'location' => 'required|string|max:255',

            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            'description' => 'required|string|max:500',

            'photos' => 'nullable|array|max:2',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $photoPaths = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('reports', 'public');
            }
        }

        $request->user()->reports()->create([
            'category' => $validated['category'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'description' => $validated['description'],
            'photos' => $photoPaths,
            'status' => 'Menunggu',
        ]);

        return back()->with(
            'success',
            'Laporan berhasil dikirim!'
        );
    }

    public function index(Request $request)
    {
        $reports = $request->user()
            ->reports()
            ->latest()
            ->paginate(10);

        return response()->json($reports);
    }

    
    public function show(Request $request, $id)
    {
        $report = $request->user()
            ->reports()
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail laporan berhasil diambil.',
            'data' => $report,
        ]);
    }

    public function dashboard(Request $request)
    {
        $reports = $request->user()->reports();

        $summary = [
            'total_laporan' => (clone $reports)->count(),

            'menunggu' => (clone $reports)
                ->where('status', 'Menunggu')
                ->count(),

            'diproses' => (clone $reports)
                ->where('status', 'Diproses')
                ->count(),

            'selesai' => (clone $reports)
                ->where('status', 'Selesai')
                ->count(),
        ];

        $latestReports = (clone $reports)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'message' => 'Data dashboard berhasil diambil.',
            'summary' => $summary,
            'latest_reports' => $latestReports,
        ]);
    }

    
    public function explore(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',

            'category' => [
                'nullable',
                Rule::in([
                    'Infrastruktur',
                    'Penerangan',
                    'Transportasi',
                    'Kebersihan',
                    'Air & Drainase',
                    'Lainnya',
                ]),
            ],
        ]);

        $reports = Report::query()
            ->select([
                'id',
                'category',
                'location',
                'latitude',
                'longitude',
                'description',
                'photos',
                'status',
                'created_at',
            ]);

        if (!empty($validated['search'])) {
            $search = $validated['search'];

            $reports->where(function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if (!empty($validated['category'])) {
            $reports->where('category', $validated['category']);
        }

        $results = $reports
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'message' => 'Data eksplorasi laporan berhasil diambil.',
            'data' => $results,
        ]);
    }

    
    public function insights()
    {
        $totalReports = Report::count();

        $completedReports = Report::where('status', 'Selesai')
            ->count();

        $inProgressReports = Report::where('status', 'Diproses')
            ->count();

        $waitingReports = Report::where('status', 'Menunggu')
            ->count();

        // Menghitung jumlah laporan berdasarkan kategori
        $categoryStats = Report::query()
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Mengambil lima laporan terbaru
        $latestReports = Report::query()
            ->select([
                'id',
                'category',
                'location',
                'description',
                'status',
                'created_at',
            ])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'message' => 'Data wawasan kota berhasil diambil.',

            'summary' => [
                'total_laporan' => $totalReports,
                'laporan_selesai' => $completedReports,
                'dalam_proses' => $inProgressReports,
                'menunggu' => $waitingReports,
            ],

            'kategori_teratas' => $categoryStats->first(),

            'distribusi_laporan' => $categoryStats,

            'laporan_terbaru' => $latestReports,
        ]);
    }

    public function history(Request $request, $id)
    {
        $report = $request->user()
            ->reports()
            ->with('statusHistories.user:id,name')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Riwayat status laporan berhasil diambil.',
            'data' => [
                'report' => [
                    'id' => $report->id,
                    'category' => $report->category,
                    'location' => $report->location,
                    'status' => $report->status,
                ],
                'history' => $report->statusHistories,
            ],
        ]);
    }
}

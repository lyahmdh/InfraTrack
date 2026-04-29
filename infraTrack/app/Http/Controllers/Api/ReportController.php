<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\StoreReportRequest;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReportImage;
use App\Mail\ReportStatusUpdatedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with([
            'user',
            'category',
            'images'
        ])
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    public function show(string $id)
    {
        $report = Report::with([
            'user',
            'category',
            'images',
            'comments.user',
            'projectUpdates.images'
        ])->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Report tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function store(StoreReportRequest $request)
    {
        $validated = $request->validated();

        // duplicate detection (30 meter)
        $duplicate = Report::where('user_id', auth()->id())
            ->where('category_id', $validated['category_id'])
            ->whereRaw("
                ST_Distance_Sphere(
                    POINT(longitude, latitude),
                    POINT(?, ?)
                ) <= ?
            ", [
                $validated['longitude'],
                $validated['latitude'],
                30
            ])
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melaporkan lokasi ini'
            ], 422);
        }

        $category = Category::find($validated['category_id']);

        // report count berdasarkan radius kategori
        $reportCount = Report::where('category_id', $category->id)
            ->whereRaw("
                ST_Distance_Sphere(
                    POINT(longitude, latitude),
                    POINT(?, ?)
                ) <= ?
            ", [
                $validated['longitude'],
                $validated['latitude'],
                $category->report_radius
            ])
            ->distinct('user_id')
            ->count('user_id');

        // severity score
        $severityScore = match ((int) $validated['severity']) {
            1 => 30,
            2 => 60,
            3 => 100,
        };

        // report count score
        $reportCountScore =
            min($reportCount, 10) * 10;

        // waiting score awal
        $waitingScore = 0;

        // final priority score
        $priorityScore =
            ($severityScore * 0.5) +
            ($reportCountScore * 0.3) +
            ($waitingScore * 0.2);

        DB::beginTransaction();

        try {

            $report = Report::create([
                'user_id' => auth()->id(),
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'severity' => $validated['severity'],
                'is_anonymous' => $validated['is_anonymous'],
                'status' => 'masuk',
                'location_name' => $validated['location_name'],
                'district' => $validated['district'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'priority_score' => round($priorityScore, 2)
            ]);

            foreach ($validated['images'] as $image) {

                $path = $image->store(
                    'reports',
                    'public'
                );

                ReportImage::create([
                    'report_id' => $report->id,
                    'image_path' => $path
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dibuat',
                'data' => $report->load([
                    'images',
                    'category'
                ])
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Report tidak ditemukan'
            ], 404);
        }

        if ($report->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($report->status !== 'masuk') {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak dapat dihapus'
            ], 422);
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dihapus'
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Report tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'status' => [
                'required',
                'in:diverifikasi,ditindaklanjuti,selesai,ditolak'
            ]
        ]);

        $report->update([
            'status' => $request->status
        ]);

        // kirim email
        Mail::to($report->user->email)
            ->send(
                new ReportStatusUpdatedMail($report)
            );

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'data' => $report
        ]);
    }
}
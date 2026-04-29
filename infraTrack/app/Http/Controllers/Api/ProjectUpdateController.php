<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectUpdate\StoreProjectUpdateRequest;
use App\Models\ProjectUpdate;
use App\Models\ProjectUpdateImage;
use Illuminate\Support\Facades\DB;

class ProjectUpdateController extends Controller
{
    public function store(StoreProjectUpdateRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {

            $update = ProjectUpdate::create([
                'report_id' => $validated['report_id'],
                'admin_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description']
            ]);

            if (!empty($validated['images'])) {

                foreach ($validated['images'] as $image) {

                    $path = $image->store(
                        'project-updates',
                        'public'
                    );

                    ProjectUpdateImage::create([
                        'project_update_id' => $update->id,
                        'image_path' => $path
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Update proyek berhasil dibuat',
                'data' => $update->load('images')
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat update proyek',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LandController extends Controller
{
    /**
     * Display a listing of land based on type.
     */
    public function index(string $type)
    {
        $tableMap = [
            'sawah' => 'agrisawah_ar_50k',
            'ladang' => 'agriladang_ar_50k',
            'kebun' => 'agrikebun_ar_50k',
        ];

        if (!isset($tableMap[$type])) {
            abort(404);
        }

        $tableName = $tableMap[$type];

        // Fetch land data with join to kecamatan if possible or just attributes
        $data = DB::table($tableName)
            ->select(
                'gid',
                'namobj',
                'remark',
                DB::raw('ST_AsGeoJSON(geom) as geojson'),
                DB::raw('ST_Area(geom::geography) as luas_m2')
            )
            ->paginate(10);

        return Inertia::render('admin/LandManagement', [
            'landData' => $data,
            'type' => $type,
            'title' => 'Manajemen Lahan ' . ucfirst($type)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $type)
    {
        $tableMap = [
            'sawah' => 'agrisawah_ar_50k',
            'ladang' => 'agriladang_ar_50k',
            'kebun' => 'agrikebun_ar_50k',
        ];

        if (!isset($tableMap[$type])) {
            abort(404);
        }

        $tableName = $tableMap[$type];

        $request->validate([
            'namobj' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'geojson' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($tableName, $request) {
                $id = DB::table($tableName)->insertGetId([
                    'namobj' => $request->namobj,
                    'remark' => $request->remark,
                ], 'gid');

                DB::statement(
                    "UPDATE {$tableName} SET geom = ST_SetSRID(ST_Multi(ST_Force4D(ST_GeomFromGeoJSON(?))), 4326) WHERE gid = ?",
                    [$request->geojson, $id]
                );

                // Sinkronisasi kecamatan_gid secara otomatis
                DB::statement("
                    UPDATE {$tableName} l
                    SET kecamatan_gid = k.gid
                    FROM kecamatan k
                    WHERE ST_Intersects(l.geom, k.geom)
                    AND l.gid = ?
                ", [$id]);
            });

            Cache::forget('all_agri_data_v1');

            return back()->with('success', 'Data lahan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $type, $gid)
    {
        $tableMap = [
            'sawah' => 'agrisawah_ar_50k',
            'ladang' => 'agriladang_ar_50k',
            'kebun' => 'agrikebun_ar_50k',
        ];

        if (!isset($tableMap[$type])) {
            abort(404);
        }

        $tableName = $tableMap[$type];

        $request->validate([
            'namobj' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'geojson' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($tableName, $request, $gid) {
                DB::table($tableName)
                    ->where('gid', (int) $gid)
                    ->update([
                        'namobj' => $request->namobj,
                        'remark' => $request->remark,
                    ]);

                if ($request->filled('geojson')) {
                    DB::statement(
                        "UPDATE {$tableName} SET geom = ST_SetSRID(ST_Multi(ST_Force4D(ST_GeomFromGeoJSON(?))), 4326) WHERE gid = ?",
                        [$request->geojson, (int) $gid]
                    );
                }

                // Selalu pastikan kecamatan_gid sinkron (terutama jika geom atau data lain berubah)
                DB::statement("
                    UPDATE {$tableName} l
                    SET kecamatan_gid = k.gid
                    FROM kecamatan k
                    WHERE ST_Intersects(l.geom, k.geom)
                    AND l.gid = ?
                ", [(int) $gid]);
            });

            Cache::forget('all_agri_data_v1');

            return back()->with('success', 'Data lahan berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error("Gagal update lahan {$tableName}: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $type, $gid)
    {
        $tableMap = [
            'sawah' => 'agrisawah_ar_50k',
            'ladang' => 'agriladang_ar_50k',
            'kebun' => 'agrikebun_ar_50k',
        ];

        if (!isset($tableMap[$type])) {
            abort(404);
        }

        $tableName = $tableMap[$type];

        try {
            DB::table($tableName)->where('gid', (int) $gid)->delete();

            Cache::forget('all_agri_data_v1');

            return back()->with('success', 'Data lahan berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error("Gagal hapus lahan {$tableName}: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

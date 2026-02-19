<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class KecamatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kecamatan = Kecamatan::select(
            'gid',
            'namobj',
            'remark',
            'wadmkk',
            'wadmpr',
            DB::raw('ST_AsGeoJSON(geom) as geojson'),
            DB::raw('ST_Area(geom::geography) as luas_m2')
        )->orderBy('namobj')->paginate(10);

        return Inertia::render('admin/Kecamatan', [
            'kecamatan' => $kecamatan
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'namobj' => 'required|string|max:255',
            'remark' => 'nullable|string',
            'wadmkk' => 'nullable|string',
            'wadmpr' => 'nullable|string',
            'geojson' => 'required|string', // Required for new data
        ]);

        DB::transaction(function () use ($request) {
            $id = DB::table('kecamatan')->insertGetId([
                'namobj' => $request->namobj,
                'remark' => $request->remark,
                'wadmkk' => $request->wadmkk,
                'wadmpr' => $request->wadmpr,
            ], 'gid');

            DB::statement(
                "UPDATE kecamatan SET geom = ST_SetSRID(ST_Multi(ST_Force4D(ST_GeomFromGeoJSON(?))), 4326) WHERE gid = ?",
                [$request->geojson, $id]
            );
        });

    return back()->with('success', 'Data kecamatan berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $gid)
    {
        $request->validate([
            'namobj' => 'required|string|max:255',
            'remark' => 'nullable|string',
            'wadmkk' => 'nullable|string',
            'wadmpr' => 'nullable|string',
            'geojson' => 'nullable|string', // GeoJSON string for geometry update
        ]);

        try {
            $kecamatan = Kecamatan::findOrFail($gid);

            DB::transaction(function () use ($request, $gid, $kecamatan) {
                $kecamatan->update([
                    'namobj' => $request->namobj,
                    'remark' => $request->remark,
                    'wadmkk' => $request->wadmkk,
                    'wadmpr' => $request->wadmpr,
                ]);

                if ($request->filled('geojson')) {
                    DB::statement(
                        "UPDATE kecamatan SET geom = ST_SetSRID(ST_Multi(ST_Force4D(ST_GeomFromGeoJSON(?))), 4326) WHERE gid = ?",
                        [$request->geojson, (int) $gid]
                    );
                }
            });

            return back()->with('success', 'Data kecamatan berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Gagal update kecamatan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($gid)
    {
        try {
            $kecamatan = Kecamatan::findOrFail($gid);
            $kecamatan->delete();

            return back()->with('success', 'Data kecamatan berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Gagal hapus kecamatan: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

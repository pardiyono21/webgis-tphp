<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan; // Panggil Modelnya
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class WebController extends Controller
{
    /**
     * Halaman Utama (Landing Page)
     */
    public function index()
    {
        // Ambil statistik ringkas untuk ditampilkan di Home
        $stats = [
            'kecamatan' => Kecamatan::count(),
            'sawah' => DB::table('agrisawah_ar_50k')->count(),
            'ladang' => DB::table('agriladang_ar_50k')->count(),
            'kebun' => DB::table('agrikebun_ar_50k')->count(),
        ];

        return Inertia::render('Home', [
            'stats' => $stats
        ]);
    }

    /**
     * Halaman Peta Interaktif
     */
    public function map()
    {
        // Menggunakan Eloquent Model - Dimuat segera untuk render awal sidebar
        $kecamatan = Kecamatan::select(
                'gid', 
                'namobj', 
                // Gunakan ST_Force2D untuk memastikan output GeoJSON bersih
                DB::raw('ST_AsGeoJSON(ST_SimplifyPreserveTopology(ST_Force2D(geom), 0.0001)) as geojson'),
                DB::raw('ST_Area(geom::geography) as luas_m2')
            )
            ->get();

        // Optimasi: Cache semua data pertanian dalam satu paket agar atomic dan lebih cepat di-deserialize
        $agriData = Cache::remember('all_agri_data_v1', 86400, function() {
            return [
                'sawah' => DB::table('agrisawah_ar_50k')->select('gid', 'namobj', 'remark', 'kecamatan_gid', DB::raw('ST_AsGeoJSON(ST_Simplify(ST_Force2D(geom), 0.00005), 6) as geojson'), DB::raw('ST_Area(geom::geography) as luas_m2'))->get(),
                'ladang' => DB::table('agriladang_ar_50k')->select('gid', 'namobj', 'remark', 'kecamatan_gid', DB::raw('ST_AsGeoJSON(ST_Simplify(ST_Force2D(geom), 0.00005), 6) as geojson'), DB::raw('ST_Area(geom::geography) as luas_m2'))->get(),
                'kebun' => DB::table('agrikebun_ar_50k')->select('gid', 'namobj', 'remark', 'kecamatan_gid', DB::raw('ST_AsGeoJSON(ST_Simplify(ST_Force2D(geom), 0.00005), 6) as geojson'), DB::raw('ST_Area(geom::geography) as luas_m2'))->get(),
            ];
        });

        return Inertia::render('Peta', [
            'petaBungo' => $kecamatan,
            // Menggunakan Deferred Props (Inertia v2)
            'agriSawah' => Inertia::defer(fn () => $agriData['sawah']),
            'agriLadang' => Inertia::defer(fn () => $agriData['ladang']),
            'agriKebun' => Inertia::defer(fn () => $agriData['kebun']),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use Illuminate\Http\Request;

class KontakLookupController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $kontak = Kontak::where('nama', 'like', "%{$query}%")
            ->select('id', 'nama', 'nomor_hp', 'instansi', 'jabatan')
            ->limit(10)
            ->get();

        return response()->json($kontak);
    }
}

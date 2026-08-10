<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserLookupController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $users = User::where('name', 'like', "%{$query}%")
            ->select('id', 'name')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}

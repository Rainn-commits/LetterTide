<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    public function submitRequest(Request $request)
    {
        $admins = User::whereHas('role', function($q) {
            $q->whereIn('nama_role', ['Admin', 'Super Admin']);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new ResetPasswordRequested());
        }    

        session()->flash('reset_requested', true);

        return redirect()->back();
    }
}
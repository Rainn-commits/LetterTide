<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ResetPasswordRequested;

class ForgotPasswordController extends Controller
{
    public function submitRequest(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        $admins = User::whereHas('role', function ($q) {
            $q->whereIn('nama_role', ['Admin', 'Super Admin']);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new ResetPasswordRequested($user));
        }

        session()->flash('reset_requested', true);

        return redirect()->back();
    }
}

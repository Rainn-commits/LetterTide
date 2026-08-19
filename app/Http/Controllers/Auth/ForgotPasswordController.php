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
            $q->whereIn('nama_role', ['Admin', 'Super Admin', 'superadmin']);
        })->get();

        foreach ($admins as $admin) {
            DB::table('notifications')->insert([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\Notifications\ResetPasswordRequested',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $admin->id,
                'data'            => json_encode([
                    'title'   => 'Permintaan Reset Password',
                    'message' => 'Ada pengguna yang meminta reset password.',
                ]),
                'read_at'         => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        session()->flash('reset_requested', true);

        return redirect()->back();
    }
}
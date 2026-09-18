<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $userRole = $user->role->nama_role ?? '';
        $isAdminOrSuperAdmin = in_array($userRole, ['Super Admin', 'Admin']);

        $rules = [
            'name' => ['required', 'string', 'max:100'],
        ];

        if ($isAdminOrSuperAdmin) {
            $rules['email'] = [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($user->id)
            ];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];

        if ($isAdminOrSuperAdmin) {
            $user->email = $validated['email'];
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $userRole = $user->role->nama_role ?? '';
        $isAdminOrSuperAdmin = in_array($userRole, ['Super Admin', 'Admin']);

        if (!$isAdminOrSuperAdmin) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah password di sini.');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.'])->withInput();
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = User::with('role')->orderBy('name')->get();

        return view('pengguna.index', compact('pengguna'));
    }

    public function create()
    {
        $roles = $this->rolesYangBolehDiassign();

        return view('pengguna.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id'  => ['required', 'exists:roles,id'],
        ]);

        $this->cegahPenugasanSuperAdminTanpaIzin($validated['role_id']);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id'  => $validated['role_id'],
            'is_aktif' => true,
        ]);

        LogAktivitas::catat('Membuat', 'Pengguna', "Menambahkan pengguna \"{$validated['name']}\"");

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $pengguna)
    {
        $this->cegahEditSuperAdminOlehAdmin($pengguna);

        $roles = $this->rolesYangBolehDiassign();

        return view('pengguna.edit', compact('pengguna', 'roles'));
    }

    public function update(Request $request, User $pengguna)
    {
        $this->cegahEditSuperAdminOlehAdmin($pengguna);

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:100', 'unique:users,email,' . $pengguna->id],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $this->cegahPenugasanSuperAdminTanpaIzin($validated['role_id']);

        $pengguna->update([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'role_id' => $validated['role_id'],
        ]);

        LogAktivitas::catat('Mengubah', 'Pengguna', "Mengubah data pengguna \"{$pengguna->name}\"");

        return redirect()->route('pengguna.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $pengguna)
    {
        $this->cegahEditSuperAdminOlehAdmin($pengguna);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $pengguna->update(['password' => Hash::make($validated['password'])]);

        LogAktivitas::catat('Reset Password', 'Pengguna', "Reset password untuk \"{$pengguna->name}\"");

        return back()->with('success', "Password untuk {$pengguna->name} berhasil direset.");
    }

    public function destroy(User $pengguna)
    {
        $this->cegahEditSuperAdminOlehAdmin($pengguna);

        if ($pengguna->id === Auth::id()) {
            return back()->withErrors(['pengguna' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $pengguna->update(['is_aktif' => false]);

        LogAktivitas::catat('Menonaktifkan', 'Pengguna', "Menonaktifkan pengguna \"{$pengguna->name}\"");

        return back()->with('success', "Pengguna {$pengguna->name} berhasil dinonaktifkan.");
    }

    private function cegahPenugasanSuperAdminTanpaIzin(int $roleId): void
    {
        $roleDipilih = Role::find($roleId);
        $userSaatIni = User::with('role')->find(Auth::id());

        if ($roleDipilih && $roleDipilih->nama_role === 'Super Admin' 
            && $userSaatIni?->role?->nama_role !== 'Super Admin') {
            abort(403, 'Hanya Super Admin yang dapat menetapkan role Super Admin.');
        }
    }

    private function cegahEditSuperAdminOlehAdmin(User $pengguna): void
    {
        $pengguna->load('role');
        $userSaatIni = User::with('role')->find(Auth::id());

        if ($pengguna->role?->nama_role === 'Super Admin' 
            && $userSaatIni?->role?->nama_role !== 'Super Admin') {
            abort(403, 'Anda tidak memiliki izin untuk mengubah akun Super Admin.');
        }
    }

    private function rolesYangBolehDiassign()
    {
        $userSaatIni = User::with('role')->find(Auth::id());

        if ($userSaatIni?->role?->nama_role === 'Super Admin') {
            return Role::orderBy('nama_role')->get();
        }

        return Role::where('nama_role', '!=', 'Super Admin')->orderBy('nama_role')->get();
    }
}

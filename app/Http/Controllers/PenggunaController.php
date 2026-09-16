<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = User::with('role')->orderBy('name')->get();

        $idPenggunaPunyaSurat = Surat::whereIn('user_id', $pengguna->pluck('id'))
            ->orWhereIn('ttd_by_user_id', $pengguna->pluck('id'))
            ->get(['user_id', 'ttd_by_user_id'])
            ->flatMap(fn ($s) => [$s->user_id, $s->ttd_by_user_id])
            ->filter()
            ->unique()
            ->values();

        return view('pengguna.index', compact('pengguna', 'idPenggunaPunyaSurat'));
    }

    public function create()
    {
        $roles = $this->rolesUntukTambahPengguna();

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
        $this->cegahPenugasanAdminOlehAdmin($validated['role_id']);

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

        $roles = $this->rolesUntukTambahPengguna();

        return view('pengguna.edit', compact('pengguna', 'roles'));
    }

    public function update(Request $request, User $pengguna)
    {
        $this->cegahEditSuperAdminOlehAdmin($pengguna);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email,' . $pengguna->id],
            'role_id'  => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $this->cegahPenugasanSuperAdminTanpaIzin($validated['role_id']);

        $dataUpdate = [
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        $passwordDiubah = !empty($validated['password']);

        if ($passwordDiubah) {
            $dataUpdate['password'] = Hash::make($validated['password']);
        }

        $pengguna->update($dataUpdate);

        $keterangan = "Mengubah data pengguna \"{$pengguna->name}\"" . ($passwordDiubah ? ' (termasuk reset password)' : '');
        LogAktivitas::catat('Mengubah', 'Pengguna', $keterangan);

        return redirect()->route('pengguna.index')
            ->with('success', 'Data pengguna berhasil diperbarui.' . ($passwordDiubah ? ' Password baru sudah aktif.' : ''));
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

    public function aktifkan(User $pengguna)
    {
        $this->cegahEditSuperAdminOlehAdmin($pengguna);

        $pengguna->update(['is_aktif' => true]);

        LogAktivitas::catat('Mengaktifkan', 'Pengguna', "Mengaktifkan kembali pengguna \"{$pengguna->name}\"");

        return back()->with('success', "Pengguna {$pengguna->name} berhasil diaktifkan kembali.");
    }
    
    public function hapusPermanen(User $pengguna)
    {
        $this->cegahEditSuperAdminOlehAdmin($pengguna);

        if ($pengguna->id === Auth::id()) {
            return back()->withErrors(['pengguna' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $punyaSurat = Surat::where('user_id', $pengguna->id)
            ->orWhere('ttd_by_user_id', $pengguna->id)
            ->exists();

        if ($punyaSurat) {
            return back()->withErrors([
                'pengguna' => "Pengguna \"{$pengguna->name}\" tidak dapat dihapus permanen karena masih tercatat sebagai pembuat/penandatangan surat. Gunakan \"Nonaktifkan\" saja."
            ]);
        }

        $nama = $pengguna->name;
        $pengguna->delete();

        LogAktivitas::catat('Menghapus', 'Pengguna', "Menghapus permanen pengguna \"{$nama}\"");

        return back()->with('success', "Pengguna {$nama} berhasil dihapus permanen.");
    }

    private function cegahPenugasanSuperAdminTanpaIzin(int $roleId): void
    {
        $roleDipilih = Role::find($roleId);
        $userSaatIni = User::with('role')->find(Auth::id());

        if (
            $roleDipilih &&
            $roleDipilih->nama_role === 'Super Admin' &&
            $userSaatIni?->role?->nama_role !== 'Super Admin'
        ) {
            abort(403, 'Hanya Super Admin yang dapat menetapkan role Super Admin.');
        }
    }

    private function cegahPenugasanAdminOlehAdmin(int $roleId): void
    {
        $roleDipilih = Role::find($roleId);
        $userSaatIni = User::with('role')->find(Auth::id());

        if (
            $roleDipilih?->nama_role === 'Admin' &&
            $userSaatIni?->role?->nama_role !== 'Super Admin'
        ) {
            abort(403, 'Hanya Super Admin yang dapat membuat pengguna dengan role Admin.');
        }
    }

    private function cegahEditSuperAdminOlehAdmin(User $pengguna): void
    {
        $pengguna->load('role');
        $userSaatIni = User::with('role')->find(Auth::id());

        if (
            $pengguna->role?->nama_role === 'Super Admin' &&
            $userSaatIni?->role?->nama_role !== 'Super Admin'
        ) {
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

    private function rolesUntukTambahPengguna()
    {
        $userSaatIni = User::with('role')->find(Auth::id());

        if ($userSaatIni?->role?->nama_role === 'Super Admin') {
            return Role::where('nama_role', '!=', 'Super Admin')
                ->orderBy('nama_role')
                ->get();
        }

        return Role::where('nama_role', 'User')
            ->get();
    }
}

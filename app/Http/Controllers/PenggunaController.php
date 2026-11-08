<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Role;
use App\Models\Surat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    private const ROLE_DILINDUNGI = ['Super Admin', 'Admin'];

    public function index(Request $request)
    {
        $me = $this->saya();
        $daftarUnit = UnitKerja::orderBy('id')->get();

        $query = User::with(['role', 'unitKerja'])->orderBy('name');
        $filterUnit = null;

        if ($me->isGlobal()) {
            if ($request->filled('unit')) {
                $filterUnit = $request->input('unit') === 'none' ? 'none' : (int) $request->input('unit');

                $filterUnit === 'none'
                    ? $query->whereNull('unit_kerja_id')
                    : $query->where('unit_kerja_id', $filterUnit);
            }
        } else {
            $query->where('unit_kerja_id', $me->unit_kerja_id)
                ->whereHas('role', fn ($q) => $q->whereNotIn('nama_role', self::ROLE_DILINDUNGI));
        }

        $pengguna = $query->get();

        $idPenggunaPunyaSurat = Surat::whereIn('user_id', $pengguna->pluck('id'))
            ->orWhereIn('ttd_by_user_id', $pengguna->pluck('id'))
            ->get(['user_id', 'ttd_by_user_id'])
            ->flatMap(fn ($s) => [$s->user_id, $s->ttd_by_user_id])
            ->filter()
            ->unique()
            ->values();

        return view('pengguna.index', compact('pengguna', 'idPenggunaPunyaSurat', 'daftarUnit', 'filterUnit'));
    }

    public function create()
    {
        $roles = $this->rolesUntukTambahPengguna();
        $daftarUnit = UnitKerja::orderBy('id')->get();

        return view('pengguna.create', compact('roles', 'daftarUnit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:100', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:8'],
            'role_id'       => ['required', 'exists:roles,id'],
            'unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
        ], [
            'unit_kerja_id.required' => 'Unit kerja wajib dipilih.',
        ]);

        $this->cegahPenugasanSuperAdminTanpaIzin($validated['role_id']);

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
            'role_id'       => $validated['role_id'],
            'unit_kerja_id' => $validated['unit_kerja_id'],
            'is_aktif'      => true,
        ]);

        $namaUnit = UnitKerja::find($validated['unit_kerja_id'])?->nama;
        LogAktivitas::catat('Membuat', 'Pengguna', "Menambahkan pengguna \"{$validated['name']}\" (unit {$namaUnit})");

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $pengguna)
    {
        $this->pastikanBolehDikelola($pengguna);

        $roles = $this->rolesUntukTambahPengguna();
        $daftarUnit = UnitKerja::orderBy('id')->get();

        return view('pengguna.edit', compact('pengguna', 'roles', 'daftarUnit'));
    }

    public function update(Request $request, User $pengguna)
    {
        $this->pastikanBolehDikelola($pengguna);

        $me = $this->saya();

        if (!$me->isGlobal()) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:100'],
            ]);

            $pengguna->update(['name' => $validated['name']]);

            LogAktivitas::catat('Mengubah', 'Pengguna', "Mengubah nama pengguna \"{$pengguna->name}\"");

            return redirect()->route('pengguna.index')
                ->with('success', 'Nama pengguna berhasil diperbarui.');
        }

        $adalahSuperAdmin = ($pengguna->role?->nama_role ?? '') === 'Super Admin';

        $rules = [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email,' . $pengguna->id],
            'role_id'  => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if (!$adalahSuperAdmin) {
            $rules['unit_kerja_id'] = ['required', 'exists:unit_kerja,id'];
        }

        $validated = $request->validate($rules, [
            'unit_kerja_id.required' => 'Unit kerja wajib dipilih.',
        ]);

        $this->cegahPenugasanSuperAdminTanpaIzin($validated['role_id']);

        $dataUpdate = [
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        if (!$adalahSuperAdmin) {
            $dataUpdate['unit_kerja_id'] = $validated['unit_kerja_id'];
        }

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
        $this->pastikanBolehDikelola($pengguna);

        if ($pengguna->id === Auth::id()) {
            return back()->withErrors(['pengguna' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $pengguna->update(['is_aktif' => false]);

        LogAktivitas::catat('Menonaktifkan', 'Pengguna', "Menonaktifkan pengguna \"{$pengguna->name}\"");

        return back()->with('success', "Pengguna {$pengguna->name} berhasil dinonaktifkan.");
    }

    public function aktifkan(User $pengguna)
    {
        $this->pastikanBolehDikelola($pengguna);

        $pengguna->update(['is_aktif' => true]);

        LogAktivitas::catat('Mengaktifkan', 'Pengguna', "Mengaktifkan kembali pengguna \"{$pengguna->name}\"");

        return back()->with('success', "Pengguna {$pengguna->name} berhasil diaktifkan kembali.");
    }

    public function hapusPermanen(User $pengguna)
    {
        $this->pastikanBolehDikelola($pengguna);

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

    private function saya(): User
    {
        return User::with(['role.permissions', 'unitKerja'])->findOrFail(Auth::id());
    }

    private function pastikanBolehDikelola(User $target): void
    {
        $me = $this->saya();
        $target->loadMissing('role');

        if ($me->isGlobal()) {
            return;
        }

        $namaRole = $target->role?->nama_role ?? '';

        if (in_array($namaRole, self::ROLE_DILINDUNGI, true)) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah akun dengan role tersebut.');
        }

        if ($me->unit_kerja_id === null || (int) $target->unit_kerja_id !== (int) $me->unit_kerja_id) {
            abort(403, 'Anda hanya dapat mengelola pengguna di unit kerja Anda sendiri.');
        }
    }

    private function cegahPenugasanSuperAdminTanpaIzin(int $roleId): void
    {
        $roleDipilih = Role::find($roleId);

        if ($roleDipilih?->nama_role === 'Super Admin' && !$this->saya()->isGlobal()) {
            abort(403, 'Hanya Super Admin yang dapat menetapkan role Super Admin.');
        }
    }

    private function rolesUntukTambahPengguna()
    {
        return Role::where('nama_role', '!=', 'Super Admin')
            ->orderBy('nama_role')
            ->get();
    }
}

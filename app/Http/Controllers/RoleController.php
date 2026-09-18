<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('nama_role')->get();

        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('id', 'asc')->get();

        return view('role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_role'      => ['required', 'string', 'max:50', 'unique:roles,nama_role'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['exists:permissions,id'],
            'retensi_arsip'  => ['nullable', 'boolean'],
        ]);

        $permissionBaru = $validated['permissions'] ?? [];

        if ($request->boolean('retensi_arsip')) {
            $idRetensiEvaluate = Permission::where('kode_permission', 'retensi.evaluate')->value('id');

            if ($idRetensiEvaluate) {
                $permissionBaru[] = $idRetensiEvaluate;
            }

            if ($validated['nama_role'] === 'Super Admin') {
                $idRetensiManage = Permission::where('kode_permission', 'retensi.manage')->value('id');

                if ($idRetensiManage) {
                    $permissionBaru[] = $idRetensiManage;
                }
            }
        }

        $permissionBaru = array_values(array_unique($permissionBaru));

        $role = Role::create([
            'nama_role' => $validated['nama_role'],
        ]);

        $role->permissions()->sync($permissionBaru);

        LogAktivitas::catat(
            'Membuat',
            'Role',
            "Membuat role \"{$role->nama_role}\""
        );

        return redirect()->route('role.index')
            ->with('success', "Role \"{$role->nama_role}\" berhasil dibuat.");
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('id', 'asc')->get();

        $permissionTerpilih = $role->permissions()
            ->pluck('permissions.id')
            ->toArray();

        return view('role.edit', compact(
            'role',
            'permissions',
            'permissionTerpilih'
        ));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nama_role'      => ['required', 'string', 'max:50', 'unique:roles,nama_role,' . $role->id],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['exists:permissions,id'],
            'retensi_arsip'  => ['nullable', 'boolean'],
        ]);

        if ($role->nama_role === 'Super Admin' && $validated['nama_role'] !== 'Super Admin') {
            return back()->withErrors([
                'nama_role' => 'Nama role "Super Admin" tidak dapat diubah.'
            ]);
        }

        $permissionBaru = $validated['permissions'] ?? [];

        if ($role->nama_role === 'Super Admin') {
            $idRoleManage = Permission::where('kode_permission', 'role.manage')->value('id');

            if ($idRoleManage && !in_array($idRoleManage, $permissionBaru)) {
                return back()->withErrors([
                    'permissions' => 'Role "Super Admin" wajib memiliki hak akses "Kelola Role & Hak Akses".'
                ]);
            }
        }

        $idRetensiEvaluate = Permission::where('kode_permission', 'retensi.evaluate')->value('id');
        $idRetensiManage = Permission::where('kode_permission', 'retensi.manage')->value('id');

        $permissionBaru = array_values(array_diff(
            $permissionBaru,
            array_filter([$idRetensiEvaluate, $idRetensiManage])
        ));

        if ($request->boolean('retensi_arsip')) {
            if ($idRetensiEvaluate) {
                $permissionBaru[] = $idRetensiEvaluate;
            }

            if ($role->nama_role === 'Super Admin' && $idRetensiManage) {
                $permissionBaru[] = $idRetensiManage;
            }
        }

        $permissionBaru = array_values(array_unique($permissionBaru));

        $role->update([
            'nama_role' => $validated['nama_role'],
        ]);

        $role->permissions()->sync($permissionBaru);

        LogAktivitas::catat(
            'Mengubah',
            'Role',
            "Mengubah hak akses role \"{$role->nama_role}\""
        );

        return redirect()->route('role.index')
            ->with('success', "Role \"{$role->nama_role}\" berhasil diperbarui.");
    }

    public function destroy(Role $role)
    {
        if (in_array($role->nama_role, ['Super Admin', 'Admin', 'User'])) {
            return back()->withErrors([
                'role' => 'Role bawaan sistem tidak dapat dihapus.'
            ]);
        }

        if ($role->users()->exists()) {
            return back()->withErrors([
                'role' => 'Role ini masih digunakan oleh pengguna aktif, tidak dapat dihapus.'
            ]);
        }

        LogAktivitas::catat(
            'Menghapus',
            'Role',
            "Menghapus role \"{$role->nama_role}\""
        );

        $role->delete();

        return back()->with(
            'success',
            "Role \"{$role->nama_role}\" berhasil dihapus."
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
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
        $permissions = Permission::orderBy('nama_permission')->get();

        return view('role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_role'      => ['required', 'string', 'max:50', 'unique:roles,nama_role'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['exists:permissions,id'],
        ]);

        $role = Role::create(['nama_role' => $validated['nama_role']]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('role.index')
            ->with('success', "Role \"{$role->nama_role}\" berhasil dibuat.");
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('nama_permission')->get();
        $permissionTerpilih = $role->permissions()->pluck('permissions.id')->toArray();

        return view('role.edit', compact('role', 'permissions', 'permissionTerpilih'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nama_role'      => ['required', 'string', 'max:50', 'unique:roles,nama_role,' . $role->id],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['exists:permissions,id'],
        ]);

        if ($role->nama_role === 'Super Admin' && $validated['nama_role'] !== 'Super Admin') {
            return back()->withErrors(['nama_role' => 'Nama role "Super Admin" tidak dapat diubah.']);
        }

        $permissionBaru = $validated['permissions'] ?? [];

        if ($role->nama_role === 'Super Admin') {
            $idRoleManage = Permission::where('kode_permission', 'role.manage')->value('id');
            if ($idRoleManage && !in_array($idRoleManage, $permissionBaru)) {
                return back()->withErrors(['permissions' => 'Role "Super Admin" wajib memiliki hak akses "Kelola Role & Hak Akses".']);
            }
        }

        $role->update(['nama_role' => $validated['nama_role']]);
        $role->permissions()->sync($permissionBaru);

        return redirect()->route('role.index')
            ->with('success', "Role \"{$role->nama_role}\" berhasil diperbarui.");
    }

    public function destroy(Role $role)
    {
        if (in_array($role->nama_role, ['Super Admin', 'Admin', 'User'])) {
            return back()->withErrors(['role' => 'Role bawaan sistem tidak dapat dihapus.']);
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'Role ini masih digunakan oleh pengguna aktif, tidak dapat dihapus.']);
        }

        $role->delete();

        return back()->with('success', "Role \"{$role->nama_role}\" berhasil dihapus.");
    }
}
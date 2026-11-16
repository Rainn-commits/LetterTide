@extends('layouts.app')

@section('page-title', 'Edit Role: ' . $role->nama_role)

@section('page-css')
    @vite('resources/css/role.css')
@endsection

@section('content')

    <div class="card">
        @if($errors->any())
            <div class="role-alert role-alert--error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('role.update', $role) }}" class="role-form">
            @csrf
            @method('PUT')

            <div class="form-field">
                <label for="nama_role">Nama Role:</label>
                <input type="text" id="nama_role" name="nama_role" value="{{ old('nama_role', $role->nama_role) }}"
                    {{ $role->nama_role === 'Super Admin' ? 'readonly' : '' }} required>
                @if($role->nama_role === 'Super Admin')
                    <p class="form-field__hint">Nama role Super Admin tidak bisa diubah.</p>
                @endif
            </div>

            <div class="form-field">
                <label>Hak Akses untuk {{ $role->nama_role }}:</label>
                <div class="permission-grid">

                    @php
                        $checkedList = old('permissions', $permissionTerpilih);
                        $isSuperAdmin = $role->nama_role === 'Super Admin';
                        $catatanSyarat = [
                            'arsip.unit'     => 'otomatis ikut Arsip Digital',
                            'surat.ttd'      => 'otomatis ikut Arsip Digital',
                            'retensi.manage' => 'otomatis ikut Retensi Arsip',
                        ];
                    @endphp

                    @foreach($permissions as $permission)
                        @php
                            $kode = $permission->kode_permission;
                            $isLockedRoleManage = $isSuperAdmin && $kode === 'role.manage';
                            $isTidakBerlaku = $isSuperAdmin
                                && in_array($kode, \App\Models\Permission::TIDAK_UNTUK_SUPER_ADMIN, true);
                            $isChecked = !$isTidakBerlaku && in_array($permission->id, $checkedList);
                        @endphp

                        <label class="permission-item {{ ($isLockedRoleManage || $isTidakBerlaku) ? 'permission-item--locked' : '' }}">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                {{ $isChecked ? 'checked' : '' }}
                                {{ ($isLockedRoleManage || $isTidakBerlaku) ? 'disabled' : '' }}>

                            <span>{{ $permission->nama_permission }}</span>

                            @if($isLockedRoleManage)
                                <small class="permission-item__note">(wajib untuk Super Admin)</small>
                            @elseif($isTidakBerlaku)
                                <small class="permission-item__note">(tidak berlaku untuk Super Admin)</small>
                            @elseif(isset($catatanSyarat[$kode]))
                                <small class="permission-item__note permission-item__note--info">({{ $catatanSyarat[$kode] }})</small>
                            @endif
                        </label>

                        @if($isLockedRoleManage)
                            <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
                        @endif
                    @endforeach

                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-dark">Simpan Perubahan</button>
                <a href="{{ route('role.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>

@endsection

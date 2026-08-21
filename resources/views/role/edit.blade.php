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
                    @foreach($permissions as $permission)
                        @php
                            $checkedList = old('permissions', $permissionTerpilih);
                            $isChecked = in_array($permission->id, $checkedList);
                            $isLockedRoleManage = $role->nama_role === 'Super Admin' && $permission->kode_permission === 'role.manage';
                        @endphp
                        <label class="permission-item {{ $isLockedRoleManage ? 'permission-item--locked' : '' }}">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                {{ $isChecked ? 'checked' : '' }}
                                {{ $isLockedRoleManage ? 'disabled' : '' }}>
                            <span>{{ $permission->nama_permission }}</span>
                            @if($isLockedRoleManage)
                                <small class="permission-item__note">(wajib untuk Super Admin)</small>
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

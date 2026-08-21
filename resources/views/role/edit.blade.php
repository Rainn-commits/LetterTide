@extends('layouts.app')
@section('content')
<h2>Edit Role: {{ $role->nama_role }}</h2>

@if($errors->any())<p style="color:red">{{ $errors->first() }}</p>@endif

<form method="POST" action="{{ route('role.update', $role) }}">
    @csrf
    @method('PUT')

    <label>Nama Role</label><br>
    <input type="text" name="nama_role" value="{{ old('nama_role', $role->nama_role) }}"
        {{ $role->nama_role === 'Super Admin' ? 'readonly' : '' }} required><br><br>

    <label>Hak Akses untuk {{ $role->nama_role }}:</label><br>
    @foreach($permissions as $permission)
        @php
            $checkedList = old('permissions', $permissionTerpilih);
            $isChecked = in_array($permission->id, $checkedList);
            $isLockedRoleManage = $role->nama_role === 'Super Admin' && $permission->kode_permission === 'role.manage';
        @endphp
        <label>
            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                {{ $isChecked ? 'checked' : '' }}
                {{ $isLockedRoleManage ? 'disabled' : '' }}>
            {{ $permission->nama_permission }}
            @if($isLockedRoleManage) <small>(wajib untuk Super Admin)</small> @endif
        </label><br>

        @if($isLockedRoleManage)
            <input type="hidden" name="permissions[]" value="{{ $permission->id }}">
        @endif
    @endforeach

    <br><button type="submit">Simpan Perubahan</button>
</form>
@endsection

@extends('layouts.app')
@section('content')
<h2>Tambah Role Baru</h2>

@if($errors->any())<p style="color:red">{{ $errors->first() }}</p>@endif

<form method="POST" action="{{ route('role.store') }}">
    @csrf

    <label>Nama Role</label><br>
    <input type="text" name="nama_role" value="{{ old('nama_role') }}" required><br><br>

    <label>Hak Akses:</label><br>
    @foreach($permissions as $permission)
        <label>
            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
            {{ $permission->nama_permission }}
        </label><br>
    @endforeach

    <br><button type="submit">Simpan</button>
</form>
@endsection

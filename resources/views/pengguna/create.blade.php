@extends('layouts.app')
@section('content')
<h2>Tambah Pengguna</h2>

@if($errors->any())<p style="color:red">{{ $errors->first() }}</p>@endif

<form method="POST" action="{{ route('pengguna.store') }}">
    @csrf
    <label>Nama</label><br>
    <input type="text" name="name" value="{{ old('name') }}" required><br>

    <label>Email</label><br>
    <input type="email" name="email" value="{{ old('email') }}" required><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br>

    <label>Role</label><br>
    <select name="role_id" required>
        <option value="">-- Pilih Role --</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                {{ $role->nama_role }}
            </option>
        @endforeach
    </select><br>

    <button type="submit">Simpan</button>
</form>
@endsection
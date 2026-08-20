@extends('layouts.app')
@section('content')
<h2>Edit Pengguna</h2>

@if($errors->any())<p style="color:red">{{ $errors->first() }}</p>@endif

<form method="POST" action="{{ route('pengguna.update', $pengguna) }}">
    @csrf
    @method('PUT')

    <label>Nama</label><br>
    <input type="text" name="name" value="{{ old('name', $pengguna->name) }}" required><br>

    <label>Email</label><br>
    <input type="email" name="email" value="{{ old('email', $pengguna->email) }}" required><br>

    <label>Role</label><br>
    <select name="role_id" required>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ old('role_id', $pengguna->role_id) == $role->id ? 'selected' : '' }}>
                {{ $role->nama_role }}
            </option>
        @endforeach
    </select><br>

    <button type="submit">Simpan Perubahan</button>
</form>
@endsection

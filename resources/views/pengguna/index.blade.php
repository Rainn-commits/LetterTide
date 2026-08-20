@extends('layouts.app')
@section('content')
<h2>Kelola Pengguna</h2>

@if(session('success'))<p>{{ session('success') }}</p>@endif
@if($errors->any())<p style="color:red">{{ $errors->first() }}</p>@endif

<a href="{{ route('pengguna.create') }}">+ Tambah Pengguna</a>

<table border="1" cellpadding="8">
    <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
    @foreach($pengguna as $p)
    <tr>
        <td>{{ $p->name }}</td>
        <td>{{ $p->email }}</td>
        <td>{{ $p->role->nama_role }}</td>
        <td>{{ $p->is_aktif ? 'Aktif' : 'Nonaktif' }}</td>
        <td>
            <a href="{{ route('pengguna.edit', $p) }}">Edit</a>

            <form method="POST" action="{{ route('pengguna.reset-password', $p) }}" style="display:inline">
                @csrf @method('PUT')
                <input type="password" name="password" placeholder="Password baru" required>
                <input type="password" name="password_confirmation" placeholder="Konfirmasi" required>
                <button type="submit">Reset Password</button>
            </form>

            @if($p->id !== auth()->id())
            <form method="POST" action="{{ route('pengguna.destroy', $p) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Nonaktifkan pengguna ini?')">Nonaktifkan</button>
            </form>
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endsection
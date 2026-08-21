@extends('layouts.app')
@section('content')
<h2>Kelola Role & Hak Akses</h2>

@if(session('success'))<p>{{ session('success') }}</p>@endif
@if($errors->any())<p style="color:red">{{ $errors->first() }}</p>@endif

<a href="{{ route('role.create') }}">+ Tambah Role Baru</a>

<table border="1" cellpadding="8">
    <tr><th>Nama Role</th><th>Jumlah Pengguna</th><th>Aksi</th></tr>
    @foreach($roles as $role)
    <tr>
        <td>{{ $role->nama_role }}</td>
        <td>{{ $role->users_count }}</td>
        <td>
            <a href="{{ route('role.edit', $role) }}">Edit / Atur Izin</a>
            @if(!in_array($role->nama_role, ['Super Admin', 'Admin', 'User']))
            <form method="POST" action="{{ route('role.destroy', $role) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus role ini?')">Hapus</button>
            </form>
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endsection

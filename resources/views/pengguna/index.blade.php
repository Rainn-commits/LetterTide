@extends('layouts.app')

@push('styles')
    @vite('resources/css/pengguna.css')
@endpush

@section('content')
<div class="pengguna-container">
    <div class="pengguna-header">
        <h2>Kelola Pengguna</h2> 
        <a href="{{ route('pengguna.create') }}" class="btn-pengguna btn-primary-pengguna">+ Tambah Pengguna</a> <!-- // Mengubah link biasa menjadi tombol -->
    </div>

    @if(session('success'))
        <div class="alert-message alert-success">{{ session('success') }}</div> <!-- // Menambah notifikasi sukses -->
    @endif

    @if($errors->any())
        <div class="alert-message alert-danger">{{ $errors->first() }}</div> <!-- // Menambah notifikasi error -->
    @endif

    <div class="table-responsive-pengguna">
        <table class="table-pengguna"> 
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengguna as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->role->nama_role }}</td>
                    <td>
                        <span class="badge-status {{ $p->is_aktif ? 'badge-active' : 'badge-inactive' }}">
                            {{ $p->is_aktif ? 'Aktif' : 'Nonaktif' }} <!-- // Memberi badge warna status -->
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('pengguna.edit', $p) }}" class="btn-pengguna btn-secondary-pengguna">Edit</a>

                        <form method="POST" action="{{ route('pengguna.reset-password', $p) }}" class="reset-form-inline"> <!-- // Merapikan form reset password inline -->
                            @csrf @method('PUT')
                            <input type="password" name="password" placeholder="Password baru" required>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi" required>
                            <button type="submit" class="btn-pengguna btn-primary-pengguna">Reset</button>
                        </form>

                        @if($p->id !== auth()->id())
                        <form method="POST" action="{{ route('pengguna.destroy', $p) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-pengguna btn-danger-pengguna" onclick="return confirm('Nonaktifkan pengguna ini?')">Nonaktifkan</button> <!-- // Mengubah tombol biasa menjadi tombol danger -->
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
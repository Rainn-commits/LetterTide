@extends('layouts.app')

@push('styles')
    @vite('resources/css/pengguna.css')
@endpush

@section('content')
<div class="pengguna-container">
    <div class="pengguna-header">
        <h2>Kelola Pengguna</h2>
        <a href="{{ route('pengguna.create') }}" class="btn-pengguna btn-primary-pengguna">
            + Tambah Pengguna
        </a>
    </div>
    @if(session('success'))
        <div class="alert-message alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-message alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="table-responsive-pengguna">
        <table class="table-pengguna">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="aksi-header">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengguna as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->email }}</td>
                    <td>
                        <span class="role-badge role-{{ strtolower(str_replace(' ', '-', $p->role->nama_role)) }}">
                            {{ $p->role->nama_role }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-status {{ $p->is_aktif ? 'badge-active' : 'badge-inactive' }}">
                            {{ $p->is_aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="aksi-cell">
                        <div class="aksi-wrapper">
                            <a href="{{ route('pengguna.edit', $p) }}" class="icon-btn" title="Edit Pengguna">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>

                            @if($p->id !== auth()->id())
                                <form method="POST"
                                      action="{{ route('pengguna.destroy', $p) }}"
                                      class="form-nonaktif">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn-danger" title="Nonaktifkan Pengguna" aria-label="Nonaktifkan Pengguna" onclick="return confirm('Nonaktifkan pengguna ini?')">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2v10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M6.34 5.34a9 9 0 1 0 11.32 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

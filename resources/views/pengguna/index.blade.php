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
                            <a href="{{ route('pengguna.edit', $p) }}"
                               class="btn-pengguna btn-secondary-pengguna">
                                Edit
                            </a>

                            @if($p->id !== auth()->id())
                                <form method="POST"
                                      action="{{ route('pengguna.destroy', $p) }}"
                                      class="form-nonaktif">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn-pengguna btn-danger-pengguna"
                                            onclick="return confirm('Nonaktifkan pengguna ini?')">
                                        Nonaktifkan
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

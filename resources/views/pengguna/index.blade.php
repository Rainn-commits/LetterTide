@extends('layouts.app')

@push('styles')
    @vite('resources/css/pengguna.css')
@endpush

@section('content')
<div class="pengguna-container">
    @if ((auth()->user()->role->nama_role ?? '') === 'Super Admin')
        <div class="km-tabs">
            <a href="{{ route('pengguna.index') }}" class="km-tabs__link is-active">Pengguna</a>
            <a href="{{ route('role.index') }}" class="km-tabs__link">Role dan Hak Akses</a>
        </div>
    @endif

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

                    @if (
                        ($authRole = auth()->user()->role->nama_role ?? '') !== 'Super Admin'
                        && ($p->role?->nama_role ?? '') === 'Super Admin'
                    )
                        @continue
                    @endif

                    <tr>
                        
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->email }}</td>
                    <td>
                        @php
                            $namaRole = $p->role?->nama_role ?? 'Tanpa Role';
                        @endphp
                        <span class="role-badge role-{{ strtolower(str_replace(' ', '-', $namaRole)) }}">
                            {{ $namaRole }}
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
                                @if ($p->is_aktif)
                                    @if (
                                        (auth()->user()->role->nama_role ?? '') === 'Super Admin'
                                        || ($p->role?->nama_role ?? '') !== 'Super Admin'
                                    )
                                        <form method="POST"
                                            action="{{ route('pengguna.destroy', $p) }}"
                                            class="form-nonaktif"
                                            data-confirm="Nonaktifkan pengguna ini?"
                                            data-confirm-variant="danger"
                                            data-confirm-label="Ya, Nonaktifkan">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn-warning" title="Nonaktifkan Pengguna" aria-label="Nonaktifkan Pengguna">
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 2v10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    <path d="M6.34 5.34a9 9 0 1 0 11.32 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <form method="POST"
                                        action="{{ route('pengguna.aktifkan', $p) }}"
                                        data-confirm="Aktifkan kembali pengguna ini?"
                                        data-confirm-label="Ya, Aktifkan">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="icon-btn" title="Aktifkan Kembali" aria-label="Aktifkan Kembali">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                @unless ($idPenggunaPunyaSurat->contains($p->id))
                                    <form method="POST"
                                        action="{{ route('pengguna.hapus-permanen', $p) }}"
                                        data-confirm="Hapus PERMANEN pengguna ini? Tindakan ini tidak bisa dibatalkan sama sekali."
                                        data-confirm-variant="danger"
                                        data-confirm-label="Ya, Hapus Permanen">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn-danger" title="Hapus Permanen" aria-label="Hapus Permanen">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endunless
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

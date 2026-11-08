@extends('layouts.app')

@push('styles')
    @vite('resources/css/pengguna.css')
@endpush

@section('content')
@php
    $saya = auth()->user();
    $lintasUnit = $saya->isGlobal();
@endphp
<div class="pengguna-container">
    @if ($lintasUnit)
        <div class="km-tabs">
            <a href="{{ route('pengguna.index') }}" class="km-tabs__link is-active">Pengguna</a>
            <a href="{{ route('role.index') }}" class="km-tabs__link">Role dan Hak Akses</a>
        </div>
    @endif

    <div class="pengguna-header">
        <div>
            <h2>Kelola Pengguna</h2>
            @unless ($lintasUnit)
                <p class="pengguna-subtitle">Unit kerja: <strong>{{ $saya->unitKerja->nama ?? '-' }}</strong></p>
            @endunless
        </div>
        @if ($lintasUnit)
            <a href="{{ route('pengguna.create') }}" class="btn-pengguna btn-primary-pengguna">
                + Tambah Pengguna
            </a>
        @endif
    </div>

    @if ($lintasUnit)
        <form method="GET" action="{{ route('pengguna.index') }}" class="pengguna-filter">
            <label for="filter-unit">Unit kerja:</label>
            <select name="unit" id="filter-unit" onchange="this.form.submit()">
                <option value="">Semua unit</option>
                @foreach ($daftarUnit as $u)
                    <option value="{{ $u->id }}" {{ (string) $filterUnit === (string) $u->id ? 'selected' : '' }}>{{ $u->nama }}</option>
                @endforeach
                <option value="none" {{ $filterUnit === 'none' ? 'selected' : '' }}>Lintas unit (tanpa unit)</option>
            </select>
        </form>
    @endif

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
                    <th>Unit Kerja</th>
                    <th>Status</th>
                    <th class="aksi-header">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengguna as $p)
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
                        @if ($p->unitKerja)
                            <span class="unit-badge unit-{{ strtolower($p->unitKerja->kode) }}">{{ $p->unitKerja->nama }}</span>
                        @else
                            <span class="unit-badge unit-semua">Semua Unit</span>
                        @endif
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
                                    @if ($lintasUnit || !in_array($p->role?->nama_role ?? '', ['Super Admin', 'Admin']))
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
                                @if ($lintasUnit && !$idPenggunaPunyaSurat->contains($p->id))
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
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">Belum ada pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

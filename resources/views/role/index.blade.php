@extends('layouts.app')

@section('page-title', 'Kelola Role dan Hak Akses')

@section('page-css')
    @vite('resources/css/role.css')
@endsection

@section('content')

    @if(session('success'))
        <div class="role-alert role-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="role-alert role-alert--error">{{ $errors->first() }}</div>
    @endif

    <div class="page-toolbar">
        <h2 class="page-title-text">Kelola Role dan Hak Akses</h2>
        <a href="{{ route('role.create') }}" class="btn-teal">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
            Tambah Role Baru
        </a>
    </div>

    <div class="card-container-outer">
        <div class="table-responsive-wrapper">
            <table class="role-table">
                <thead>
                    <tr>
                        <th>NAMA ROLE</th>
                        <th class="col-count text-center">JUMLAH PENGGUNA</th>
                        <th class="col-actions text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>
                                @php
                                    $roleClass = match($role->nama_role) {
                                        'Super Admin' => 'badge-role--super-admin',
                                        'Admin'       => 'badge-role--admin',
                                        default       => 'badge-role--user',
                                    };
                                @endphp
                                <span class="badge-role {{ $roleClass }}">
                                    {{ $role->nama_role }}
                                </span>
                            </td>
                            <td class="text-center font-bold">{{ $role->users_count }}</td>
                            <td class="text-center">
                                <div class="row-actions">
                                    <a href="{{ route('role.edit', $role) }}" class="icon-btn" title="Edit / Atur Izin">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </a>

                                    @if(!in_array($role->nama_role, ['Super Admin', 'Admin', 'User']))
                                        <form method="POST" action="{{ route('role.destroy', $role) }}" data-confirm="Hapus role ini?" data-confirm-variant="danger" data-confirm-label="Ya, Hapus">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn--danger" title="Hapus role">
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4 7H20M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2M6 7l1 13a2 2 0 002 2h6a2 2 0 002-2l1-13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="table-empty">Belum ada role.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

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
        <div class="page-toolbar__spacer"></div>
        <a href="{{ route('role.create') }}" class="btn-teal">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
            Tambah Role Baru
        </a>
    </div>

    <div class="card card--table">
        <table class="role-table">
            <thead>
                <tr>
                    <th>Nama Role</th>
                    <th class="col-count">Jumlah Pengguna</th>
                    <th class="col-actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td class="role-name">{{ $role->nama_role }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('role.edit', $role) }}" class="icon-btn" title="Edit / Atur Izin">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 20L4.6 16.2C4.7 15.5 5 14.9 5.5 14.4L15.4 4.5C16.2 3.7 17.5 3.7 18.3 4.5L19.5 5.7C20.3 6.5 20.3 7.8 19.5 8.6L9.6 18.5C9.1 19 8.5 19.3 7.8 19.4L4 20Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                    </svg>
                                </a>

                                @if(!in_array($role->nama_role, ['Super Admin', 'Admin', 'User']))
                                    <form method="POST" action="{{ route('role.destroy', $role) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn icon-btn--danger" title="Hapus role" onclick="return confirm('Hapus role ini?')">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4 7H20M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2M6 7l1 13a2 2 0 002 2h6a2 2 0 002-2l1-13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="role-badge-core">Role inti</span>
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

@endsection

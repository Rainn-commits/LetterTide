@extends('layouts.app')

@section('page-title', 'Tambah Role Baru')

@section('page-css')
    @vite('resources/css/role.css')
@endsection

@section('content')

    <div class="card">
        @if($errors->any())
            <div class="role-alert role-alert--error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('role.store') }}" class="role-form">
            @csrf

            <div class="form-field">
                <label for="nama_role">Nama Role:</label>
                <input type="text" id="nama_role" name="nama_role" value="{{ old('nama_role') }}" required>
            </div>

            <div class="form-field">
                <label>Hak Akses:</label>
                <div class="permission-grid">
                    @php
                        $catatanSyarat = [
                            'arsip.unit'     => 'otomatis ikut Arsip Digital',
                            'surat.ttd'      => 'otomatis ikut Arsip Digital',
                            'retensi.manage' => 'otomatis ikut Retensi Arsip',
                        ];
                    @endphp

                    @foreach($permissions as $permission)
                        <label class="permission-item">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                            <span>{{ $permission->nama_permission }}</span>

                            @if(isset($catatanSyarat[$permission->kode_permission]))
                                <small class="permission-item__note permission-item__note--info">({{ $catatanSyarat[$permission->kode_permission] }})</small>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-dark">Simpan</button>
                <a href="{{ route('role.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>

@endsection

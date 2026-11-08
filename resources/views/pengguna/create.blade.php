@extends('layouts.app')

@push('styles')
    @vite('resources/css/pengguna.css')
@endpush

@section('content')
<div class="pengguna-container">
    <h2>Tambah Pengguna</h2>

    @if($errors->any())
        <div class="alert-message alert-danger">{{ $errors->first() }}</div> 
    @endif

    <div class="form-card"> 
        <form method="POST" action="{{ route('pengguna.store') }}">
            @csrf
            
            <div class="form-group">
                <label>Nama:</label>
                <input type="text" name="name" class="form-control-pengguna" value="{{ old('name') }}" required> 
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control-pengguna" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" class="form-control-pengguna" required>
            </div>

            <div class="form-group">
                <label>Unit Kerja:</label>
                <select name="unit_kerja_id" class="form-control-pengguna" required>
                    <option value="">-- Pilih Unit Kerja --</option>
                    @foreach($daftarUnit as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_kerja_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->nama }}
                        </option>
                    @endforeach
                </select>
                <p style="font-size: 12.5px; color: #64748b; margin-top: 6px;">
                    Menentukan template, kop surat, dan arsip mana yang dapat diakses akun ini.
                </p>
            </div>

            <div class="form-group">
                <label>Role:</label>
                <select name="role_id" class="form-control-pengguna" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->nama_role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-pengguna btn-primary-pengguna">Simpan</button>
        </form>
    </div>
</div>
@endsection

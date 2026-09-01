@extends('layouts.app')

@push('styles')
    @vite('resources/css/pengguna.css')
@endpush

@section('content')
<div class="pengguna-container">
    <h2>Edit Pengguna</h2>

    @if($errors->any())
        <div class="alert-message alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="form-card"> 
        <form method="POST" action="{{ route('pengguna.update', $pengguna) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" class="form-control-pengguna" value="{{ old('name', $pengguna->name) }}" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control-pengguna" value="{{ old('email', $pengguna->email) }}" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role_id" class="form-control-pengguna" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $pengguna->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->nama_role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-pengguna btn-primary-pengguna">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
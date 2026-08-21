@extends('layouts.app')

@section('page-title', 'Kelola Profil')

@section('page-css')
    @vite('resources/css/profile.css')
@endsection

@section('content')

    @php
        $userRole = $user->role->nama_role ?? '';
        $isAdminOrSuperAdmin = in_array($userRole, ['Super Admin', 'Admin']);
    @endphp

    @if (session('success'))
        <div class="profile-alert">{{ session('success') }}</div>
    @endif

    <div class="profile-grid">

        <div class="card">
            <h3 class="card__title">Informasi Profil</h3>

            <div class="profile-avatar-row">
                <span class="profile-avatar">{{ Str::of($user->name ?? 'U')->explode(' ')->map(fn ($w) => strtoupper($w[0] ?? ''))->take(2)->implode('') }}</span>
                <div>
                    <div class="profile-avatar-row__name">{{ $user->name }}</div>
                    <div class="profile-avatar-row__role">{{ $user->role->nama_role ?? '-' }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" novalidate>
                @csrf
                @method('PUT')

                <div class="profile-field">
                    <label for="name">Nama Lengkap:</label>
                    <div class="profile-field__control">
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    @error('name')
                        <span class="profile-field__error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="email">Email:</label>
                    <div class="profile-field__control">
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" @if(!$isAdminOrSuperAdmin) disabled @endif required>
                    </div>
                    @error('email')
                        <span class="profile-field__error">{{ $message }}</span>
                    @enderror

                    @if (!$isAdminOrSuperAdmin)
                        <p class="profile-field__hint">Email tidak bisa diubah sendiri. Hubungi Admin hanya jika perlu diganti.</p>
                    @endif
                </div>

                <button type="submit" class="btn-profile-save">Simpan Perubahan</button>
            </form>
        </div>

    </div>

@endsection

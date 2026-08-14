@extends('layouts.app')

@section('page-title', 'Kelola Profil')

@section('page-css')
    @vite('resources/css/profile.css')
@endsection

@section('content')

    @if (session('success'))
        <div class="profile-alert">{{ session('success') }}</div>
    @endif

    <div class="profile-grid">

        {{-- Informasi profil --}}
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
                        <input type="email" id="email" value="{{ $user->email }}" disabled>
                    </div>
                    <p class="profile-field__hint">Email tidak bisa diubah sendiri. Hubungi Admin kalau perlu diganti.</p>
                </div>

                <button type="submit" class="btn-profile-save">Simpan Perubahan</button>
            </form>
        </div>

        {{-- Ubah password --}}
        <div class="card">
            <h3 class="card__title">Ubah Password</h3>

            <form method="POST" action="{{ route('profile.update-password') }}" novalidate>
                @csrf
                @method('PUT')

                <div class="profile-field">
                    <label for="current_password">Password Saat Ini:</label>
                    <div class="profile-field__control">
                        <input type="password" id="current_password" name="current_password" required>
                        <button type="button" class="profile-field__toggle" data-toggle-password="current_password" aria-label="Tampilkan password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 12C2 12 5.5 5 12 5C18.5 5 22 12 22 12C22 12 18.5 19 12 19C5.5 19 2 12 2 12Z" stroke="currentColor" stroke-width="1.6"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <span class="profile-field__error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="password">Password Baru:</label>
                    <div class="profile-field__control">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="profile-field__toggle" data-toggle-password="password" aria-label="Tampilkan password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 12C2 12 5.5 5 12 5C18.5 5 22 12 22 12C22 12 18.5 19 12 19C5.5 19 2 12 2 12Z" stroke="currentColor" stroke-width="1.6"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                        </button>
                    </div>
                    <p class="profile-field__hint">Minimal 8 karakter.</p>
                    @error('password')
                        <span class="profile-field__error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="password_confirmation">Konfirmasi Password Baru:</label>
                    <div class="profile-field__control">
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                        <button type="button" class="profile-field__toggle" data-toggle-password="password_confirmation" aria-label="Tampilkan password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 12C2 12 5.5 5 12 5C18.5 5 22 12 22 12C22 12 18.5 19 12 19C5.5 19 2 12 2 12Z" stroke="currentColor" stroke-width="1.6"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-profile-save">Ubah Password</button>
            </form>
        </div>

    </div>

@endsection

@section('page-js')
    @vite('resources/js/password-toggle.js')
@endsection

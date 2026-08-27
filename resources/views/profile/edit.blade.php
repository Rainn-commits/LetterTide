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
        <div class="profile-alert">
            <span class="profile-alert__icon">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="profile-page">

        <div class="profile-page__header">
            <div>
                <h2>Kelola Profil</h2>
                <p>Perbarui informasi akun dan data profil Anda.</p>
            </div>

            <div class="profile-page__badge">
                <span class="profile-page__badge-dot"></span>
                Akun Aktif
            </div>
        </div>

        <div class="profile-grid">
            <section class="profile-card profile-card--identity">
                <div class="profile-card__shine"></div>

                <div class="profile-identity">
                    <div class="profile-avatar">
                        {{ Str::of($user->name ?? 'U')
                            ->explode(' ')
                            ->map(fn ($w) => strtoupper($w[0] ?? ''))
                            ->take(2)
                            ->implode('') }}
                    </div>
                    
                    <div class="profile-identity__info">
                        <h3>{{ $user->name }}</h3>
                    </div>
                </div>

                <div class="profile-identity__footer">
                    <span>LetterTide System</span>
                </div>
            </section>

            <section class="profile-card profile-card--form">

                <div class="profile-card__heading">
                    <div>
                        <span class="profile-card__eyebrow">
                            INFORMASI AKUN
                        </span>

                        <h3>Informasi Profil</h3>
                    </div>
                </div>

                <form method="POST"
                      action="{{ route('profile.update') }}"
                      novalidate>

                    @csrf
                    @method('PUT')

                    <div class="profile-field">
                        <label for="name">
                            Nama Lengkap
                        </label>
                        <div class="profile-field__control">
                            <span class="profile-field__icon">
                                <svg viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8">
                                    <path d="M20 21a8 8 0 0 0-16 0"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                required
                            >
                        </div>
                        @error('name')
                            <span class="profile-field__error">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="profile-field">

                        <label for="email">
                            Email
                        </label>

                        <div class="profile-field__control">
                            <span class="profile-field__icon">
                                <svg viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path d="m3 7 9 6 9-6"/>
                                </svg>
                            </span>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                @if(!$isAdminOrSuperAdmin) disabled @endif
                                required
                            >
                        </div>

                        @error('email')
                            <span class="profile-field__error">
                                {{ $message }}
                            </span>
                        @enderror
                        @if (!$isAdminOrSuperAdmin)
                            <p class="profile-field__hint">
                                Email tidak bisa diubah sendiri.
                                Hubungi Admin jika perlu diganti.
                            </p>
                        @endif
                    </div>

                    <div class="profile-form__footer">
                        <button
                            type="submit"
                            class="btn-profile-save">

                            <span>Simpan Perubahan</span>

                            <svg viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2">
                                <path d="M5 12h14"/>
                                <path d="m13 6 6 6-6 6"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection

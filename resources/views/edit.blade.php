@extends('layouts.app')

@section('page-title', 'Edit Template Surat')

@section('page-css')
    @vite('resources/css/edit.css')
@endsection

@section('content')

    <div class="card">
        <form method="POST" action="{{ route('template-surat.update', $template->id) }}" class="template-form" novalidate>
            @csrf
            @method('PUT')

            <div class="form-field">
                <label for="nama_template">Nama Template:</label>
                <input type="text" id="nama_template" name="nama_template" value="{{ old('nama_template', $template->nama_template) }}" required autofocus>
                @error('nama_template')
                    <p class="form-field__hint" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="isi_template">Isi template:</label>
                <textarea id="isi_template" name="isi_template" required>{{ old('isi_template', $template->isi_template) }}</textarea>
                <p class="form-field__hint">
                    Gunakan format <code>@{{nama_variabel}}</code> untuk menandai bagian yang akan diisi otomatis.
                    Menyimpan perubahan akan membuat <strong>versi baru</strong> (versi lama tetap tersimpan di riwayat).
                </p>
                @error('isi_template')
                    <p class="form-field__hint" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label>Placeholder terdeteksi</label>
                <div class="placeholder-list" id="placeholderList">
                    {{-- Diisi otomatis oleh template-editor.js saat halaman dimuat --}}
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-dark">Simpan sebagai versi baru</button>
                <a href="{{ route('template-surat.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>

@endsection

@section('page-js')
    @vite('resources/js/template-editor.js')
@endsection
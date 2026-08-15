@extends('layouts.app')

@section('page-title', 'Tambah Template Baru')

@section('page-css')
    @vite('resources/css/create.css')
@endsection

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger" style="padding: 12px 16px; background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; border-radius: 8px; margin-bottom: 20px;">
            <strong style="display: block; margin-bottom: 4px;">Terjadi kesalahan input:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('template-surat.store') }}" class="template-form">
            @csrf

            <div class="form-field">
                <label for="nama_template">Nama Template:</label>
                <input 
                    type="text" 
                    id="nama_template" 
                    name="nama_template" 
                    value="{{ old('nama_template') }}" 
                    required 
                    autofocus
                >
                @error('nama_template')
                    <p class="form-field__hint" style="color: var(--color-danger, #dc3545);">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="isi_template">Isi Template:</label>
                <textarea 
                    id="isi_template" 
                    name="isi_template" 
                    rows="8" 
                    required
                >{{ old('isi_template') }}</textarea>
                
                <p class="form-field__hint">
                    Gunakan format <code>@{{nama_variabel}}</code> untuk menandai bagian yang akan diisi otomatis,
                    contoh: <code>@{{nama_karyawan}}</code>.
                </p>
                
                @error('isi_template')
                    <p class="form-field__hint" style="color: var(--color-danger, #dc3545);">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label>Placeholder Terdeteksi</label>
                <div class="placeholder-list" id="placeholderList">
                    <p class="placeholder-list__empty">Belum ada placeholder terdeteksi. Ketik @{{nama_variabel}} di isi template.</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-dark">Simpan</button>
                <a href="{{ route('template-surat.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>

@endsection

@section('page-js')
    @vite('resources/js/template-editor.js')
@endsection
@extends('layouts.app')

@section('page-title', 'Edit Template Surat')

@section('page-css')
    @vite('resources/css/template-surat.css')
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
        @php
            $item = $template ?? $templateSurat;
        @endphp

        <form method="POST" action="{{ route('template-surat.update', $item->id ?? $item) }}" class="template-form">
            @csrf
            @method('PUT')

            <div class="form-field">
                <label for="nama_template">Nama Template:</label>
                <input 
                    type="text" 
                    id="nama_template" 
                    name="nama_template" 
                    value="{{ old('nama_template', $item->nama_template) }}" 
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
                >{{ old('isi_template', $item->isi_template) }}</textarea>
                
                <p class="form-field__hint">
                    Gunakan format <code>@{{nama_variabel}}</code> untuk menandai bagian yang akan diisi otomatis.<br>
                    <em><strong>Catatan:</strong> Menyimpan perubahan akan tersimpan sebagai <strong>versi baru</strong>.</em>
                </p>
                
                @error('isi_template')
                    <p class="form-field__hint" style="color: var(--color-danger, #dc3545);">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label>Placeholder Terdeteksi</label>
                <div class="placeholder-list" id="placeholderList">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-dark">Simpan sebagai Versi Baru</button>
                <a href="{{ route('template-surat.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>

@endsection

@section('page-js')
    @vite('resources/js/template-editor.js')
@endsection
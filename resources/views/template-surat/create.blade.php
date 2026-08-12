@section('page-title', 'Tambah Template Baru')

@section('page-css')
    @vite('resources/css/create.css')
@endsection

@section('content')

    <div class="card">
        <form method="POST" action="{{ route('template-surat.store') }}" class="template-form" novalidate>
            @csrf

            <div class="form-field">
                <label for="nama_template">Nama Template:</label>
                <input type="text" id="nama_template" name="nama_template" value="{{ old('nama_template') }}" required autofocus>
                @error('nama_template')
                    <p class="form-field__hint" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="isi_template">Isi template:</label>
                <textarea id="isi_template" name="isi_template" required>{{ old('isi_template') }}</textarea>
                <p class="form-field__hint">
                    Gunakan format <code>@{{nama_variabel}}</code> untuk menandai bagian yang akan diisi otomatis,
                    contoh: <code>@{{nama_karyawan}}</code>.
                </p>
                @error('isi_template')
                    <p class="form-field__hint" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label>Placeholder terdeteksi</label>
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

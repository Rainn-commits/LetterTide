@extends('layouts.app')

@section('page-title', 'Edit Kop Surat')

@section('page-css')
    @vite('resources/css/kop-surat.css')
@endsection

@section('content')

    @if ($errors->any())
        <div style="padding: 12px 16px; background-color: #FEE2E2; color: #DC2626; border: 1px solid rgba(220,38,38,0.25); border-radius: 8px; margin-bottom: 16px; font-weight: 600;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="width: 100%; max-width: 680px; margin: 0 auto; padding: 32px; background: #ffffff; border-radius: 16px; box-shadow: 0 8px 22px -14px rgba(16,24,32,0.25);">
        <h2 style="border-bottom: none; padding-bottom: 0; margin-bottom: 24px;">Edit Kop Surat (Versi {{ $kopSurat->versi }})</h2>

        <form action="{{ route('kop-surat.update', $kopSurat->id) }}" method="POST" enctype="multipart/form-data" style="width: 100%; box-sizing: border-box;"> 
            @csrf
            @method('PUT')

            @if($kopSurat->logo)
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 8px; color: #334155;">Logo Saat Ini:</label>
                    <div style="padding: 12px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; display: inline-block;">
                        <img src="{{ asset('storage/' . $kopSurat->logo) }}" alt="Logo Kop Surat" style="max-height: 80px; width: auto; object-fit: contain;">
                    </div>
                </div>
            @endif

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: #334155;">Ganti Logo (Opsional)</label>

                <div style="display: flex; align-items: center; gap: 12px; padding: 6px 12px; border: 1px solid #CBD5E1; border-radius: 8px; background: #ffffff;">
                    <label for="logo" style="padding: 8px 16px; background: #00A3B0; color: #ffffff; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: background 0.2s;">
                        Choose File
                    </label>
                    <span id="file-name" style="color: #64748B; font-size: 0.875rem;">No file chosen</span>
                    <input type="file" name="logo" id="logo" accept="image/*" style="display: none;" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
                </div>
                
                <small style="color: #64748B; font-size: 0.75rem; display: block; margin-top: 4px;">Biarkan kosong jika tidak ingin mengubah logo.</small>
            </div>

            @include('partials.pilih-unit', [
                'nilai' => old('unit_kerja_id', $kopSurat->unit_kerja_id),
            ])

            <div style="margin-bottom: 16px;">
                <label for="nama_kop" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: #334155;">Nama Kop Surat</label>
                <input
                    type="text"
                    name="nama_kop"
                    id="nama_kop"
                    value="{{ old('nama_kop', $kopSurat->nama_kop) }}"
                    placeholder="Contoh: Kop Utama"
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;"
                >
            </div>

            <div style="margin-bottom: 16px;">
                <label for="nama_pt" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: #334155;">Nama Perusahaan / PT</label>
                <input type="text" name="nama_pt" id="nama_pt" value="{{ old('nama_pt', $kopSurat->nama_pt) }}" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label for="alamat_pt" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: #334155;">Alamat Lengkap</label>
                <textarea name="alamat_pt" id="alamat_pt" rows="3" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; box-sizing: border-box; resize: vertical; font-family: inherit; font-size: 0.9rem;">{{ old('alamat_pt', $kopSurat->alamat_pt) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label for="telepon" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: #334155;">No. Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $kopSurat->telepon) }}" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
                <div>
                    <label for="email" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: #334155;">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $kopSurat->email) }}" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label for="website" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; color: #334155;">Website (Opsional)</label>
                <input type="text" name="website" id="website" value="{{ old('website', $kopSurat->website) }}" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>

            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="submit" style="padding: 10px 24px; background: #00A3B0; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Simpan Perubahan
                </button>
                <a href="{{ route('kop-surat.index') }}" style="padding: 10px 20px; background: #E2E8F0; color: #475569; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection

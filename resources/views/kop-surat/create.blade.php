@extends('layouts.app')

@section('page-title', 'Atur Kop Surat')

@section('page-css')
    @vite('resources/css/kop-surat.css')
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto; padding: 24px; background: #ffffff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 20px; color: #0f172a;">
        {{ $kopSuratAktif ? 'Simpan sebagai Versi Baru' : 'Atur Kop Surat' }}
    </h2>

    <form method="POST" action="{{ route('kop-surat.store') }}" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label for="logo" style="font-weight: 600; color: #0f172a; margin: 0;">Logo Perusahaan</label>
                <span style="font-size: 0.8rem; color: #64748b;">PNG, JPG, JPEG (Max 10MB)</span>
            </div>

            <label for="logo" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 10px; background-color: #5eead4; color: #0f172a; font-weight: 600; border-radius: 8px; cursor: pointer; text-align: center; border: none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                Upload Logo
            </label>

            <input type="file" name="logo" id="logo" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : ''">

            <div id="file-name" style="font-size: 0.85rem; color: #334155; margin-top: 6px; font-weight: 500;"></div>

            @if($kopSuratAktif && $kopSuratAktif->logo)
                <div style="margin-top: 8px; display: flex; align-items: center; gap: 10px;">
                    <img src="{{ Storage::url($kopSuratAktif->logo) }}" width="45" style="border-radius: 4px; border: 1px solid #e2e8f0;">
                    <small style="color: #64748b;">Logo saat ini (dipakai jika tidak upload baru)</small>
                </div>
            @endif
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a;">Nama Perusahaan</label>
            <input type="text" name="nama_pt" value="{{ old('nama_pt', $kopSuratAktif->nama_pt ?? '') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;" required>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a;">Alamat</label>
            <textarea name="alamat_pt" rows="3" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;" required>{{ old('alamat_pt', $kopSuratAktif->alamat_pt ?? '') }}</textarea>
        </div>

        <div style="margin-bottom: 16px;">
            <label for="telepon" style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a;">No Telepon / WhatsApp</label>
            
            <div style="display: flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background-color: #ffffff;">
                <span style="padding: 10px 12px; background-color: #f1f5f9; color: #475569; font-weight: 600; font-size: 0.9rem; border-right: 1px solid #cbd5e1; user-select: none;">
                    +62
                </span>
                
                <input type="text" 
                    id="telepon_input" 
                    placeholder="812-3456-7890" 
                    style="width: 100%; padding: 10px 12px; border: none; outline: none; font-size: 0.95rem;" 
                    oninput="formatNoHP(this)" 
                    required>

                <input type="hidden" 
                    name="telepon" 
                    id="telepon_hidden" 
                    value="{{ old('telepon', $kopSuratAktif->telepon ?? '') }}">
            </div>
            <small style="color: #64748b; font-size: 0.8rem; margin-top: 4px; display: block;">Masukkan nomor mulai dari angka 8 (Contoh: 81234567890)</small>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a;">Email</label>
            <input type="email" name="email" value="{{ old('email', $kopSuratAktif->email ?? '') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;" required>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a;">Website</label>
            <input type="text" name="website" value="{{ old('website', $kopSuratAktif->website ?? '') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
        </div>

        <div>
            <button type="submit" style="width: 100%; padding: 12px; background-color: #0d9488; color: #ffffff; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; font-size: 0.95rem;">
                Simpan sebagai versi baru
            </button>
            <p style="text-align: center; margin-top: 8px; margin-bottom: 0;">
                <small style="color: #ef4444;">Surat yang sudah dibuat sebelumnya tidak akan berubah.</small>
            </p>
        </div>
    </form>
</div>
    <script>
        function formatNoHP(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = value.substring(1);
            }
            if (value.startsWith('62')) {
                value = value.substring(2);
            }

            let formatted = '';
            if (value.length > 0) {
                formatted = value.substring(0, 3);
            }
            if (value.length > 3) {
                formatted += '-' + value.substring(3, 7);
            }
            if (value.length > 7) {
                formatted += '-' + value.substring(7, 12);
            }

            input.value = formatted;

            const hiddenInput = document.getElementById('telepon_hidden');
            if (value.length > 0) {
                hiddenInput.value = '+62 ' + formatted;
            } else {
                hiddenInput.value = '';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const hiddenVal = document.getElementById('telepon_hidden').value;
            const inputVisual = document.getElementById('telepon_input');
            
            if (hiddenVal) {
                let cleanVal = hiddenVal.replace('+62', '').replace(/\s/g, '').trim();
                inputVisual.value = cleanVal;
                formatNoHP(inputVisual);
            }
        });
    </script>
@endsection


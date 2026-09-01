@extends('layouts.app')

@section('page-title', 'Atur Kop Surat')

@section('page-css')
    @vite('resources/css/kop-surat.css')
    @vite('resources/css/surat-preview.css')
    <style>
        .kop-grid-layout {
            display: grid !important;
            grid-template-columns: 460px 1fr !important;
            gap: 24px !important;
            align-items: start !important;
            width: 100% !important;
        }

        .kop-grid-layout form[action*="kop-surat"] {
            max-width: 100% !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            background: transparent !important;
        }

        @media (max-width: 1024px) {
            .kop-grid-layout { 
                grid-template-columns: 1fr !important; 
            }
        }
    </style>
@endsection

@section('content')
<div class="kop-grid-layout">

    <div class="card" style="padding: 24px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 20px; color: #0f172a; border-bottom: none; padding-bottom: 0;">
            Atur Kop Surat Baru
        </h2>

        <form method="POST" action="{{ route('kop-surat.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label for="logo" style="font-weight: 600; color: #0f172a; margin: 0; text-transform: none;">Logo Perusahaan</label>
                    <span style="font-size: 0.8rem; color: #64748b;">PNG, JPG, JPEG (Max 10MB)</span>
                </div>

                <label for="logo" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 80%; padding: 10px; background: linear-gradient(135deg, #00C3D0 0%, #0090A0 100%); color: #ffffff; font-weight: 600; border-radius: 8px; cursor: pointer; text-align: center; border: none; box-shadow: 0 4px 12px rgba(0,195,208,0.3); margin-top: 0; margin: 0 auto;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Upload Logo
                </label>

                <input type="file" name="logo" id="logo" accept="image/png, image/jpeg, image/jpg" style="display: none;">
                <div id="file-name" style="font-size: 0.85rem; color: #334155; margin-top: 6px; font-weight: 500;"></div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a; text-transform: none;">Nama Perusahaan</label>
                <input type="text" id="input_nama_pt" name="nama_pt" value="{{ old('nama_pt') }}" placeholder="Masukkan Nama Perusahaan" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;" required>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a; text-transform: none;">Alamat</label>
                <textarea id="input_alamat" name="alamat_pt" rows="3" placeholder="Masukkan Alamat Lengkap" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;" required>{{ old('alamat_pt') }}</textarea>
            </div>

            <div style="margin-bottom: 16px;">
                <label for="telepon_input" style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a; text-transform: none;">No Telepon / WhatsApp</label>
                <div style="display: flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background-color: #ffffff;">
                    <span style="padding: 10px 12px; background-color: #f1f5f9; color: #475569; font-weight: 600; font-size: 0.9rem; border-right: 1px solid #cbd5e1; user-select: none;">+62</span>
                    <input type="text" id="telepon_input" placeholder="812-3456-7890" style="width: 100%; padding: 10px 12px; border: none; outline: none; font-size: 0.95rem;" oninput="formatNoHP(this)" required>
                    <input type="hidden" name="telepon" id="telepon_hidden" value="{{ old('telepon') }}">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a; text-transform: none;">Email</label>
                <input type="email" id="input_email" name="email" value="{{ old('email') }}" placeholder="contoh@domain.com" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;" required>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #0f172a; text-transform: none;">Website</label>
                <input type="text" id="input_website" name="website" value="{{ old('website') }}" placeholder="www.domain.com" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
            </div>

            <button type="submit" style="display: block; width: 80%; padding: 13px; background: linear-gradient(135deg, #00C3D0 0%, #0090A0 100%); color: #ffffff; font-weight: 700; border: none; border-radius: 8px; cursor: pointer; font-size: 0.95rem; box-shadow: 0 6px 16px -4px rgba(0,195,208,0.45); margin: 0 auto;">
                Simpan Sebagai Versi Baru
            </button>
        </form>
    </div>

    <div style="position: sticky; top: 20px;">
        <label style="font-family: var(--font-display); color: #000000; font-size: 1.4rem; font-weight: 600; margin-top: 32px; margin-bottom: 16px; display: block; text-shadow: 0 1px 3px rgba(255, 255, 255, 0.9);">Live Preview Kop Surat:</label>
        <div class="surat-paper" style="width: 100%; min-height: auto; padding: 25px; box-shadow: 0 10px 30px -8px rgba(0,195,208,0.25); background: #fff; border-radius: 12px; border: 1px solid rgba(0,195,208,0.15);">
            <div class="surat-paper__header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div class="surat-paper__logo-left" style="width: 25%;">
                    <img id="prev_logo" src="" class="surat-paper__logo" style="max-height: 75px; width: auto; display: none;">
                </div>
                <div class="surat-paper__company-right" style="width: 75%; text-align: right;">
                    <h2 id="prev_nama" style="font-size: 14pt; font-weight: bold; margin: 0; text-transform: uppercase; border-bottom: none; padding-bottom: 0;">NAMA PERUSAHAAN</h2>
                    <p id="prev_alamat" style="font-size: 9pt; margin: 2px 0;">Alamat Perusahaan...</p>
                    <p style="font-size: 9pt; margin: 1px 0;">
                        <span id="prev_telepon">WhatsApp. +62 8123456789</span> &middot; 
                        <span id="prev_website">Website. www.website.com</span>
                    </p>
                    <p id="prev_email" style="font-size: 9pt; margin: 1px 0;">Email: email@domain.com</p>
                </div>
            </div>
            <div style="border-top: 2px solid #000; margin-top: 8px; margin-bottom: 20px;"></div>
            
            <div style="color: #cbd5e1; font-size: 10pt; line-height: 1.6;">
                <div style="height: 10px; background: #f1f5f9; width: 30%; margin-bottom: 10px;"></div>
                <div style="height: 8px; background: #f1f5f9; width: 100%; margin-bottom: 6px;"></div>
                <div style="height: 8px; background: #f1f5f9; width: 90%;"></div>
            </div>
        </div>
    </div>

</div>

<script>
    function formatNoHP(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.startsWith('0')) value = value.substring(1);
        if (value.startsWith('62')) value = value.substring(2);

        let formatted = value;
        if (value.length > 3) formatted = value.substring(0, 3) + '-' + value.substring(3);
        if (value.length > 7) formatted = value.substring(0, 3) + '-' + value.substring(3, 7) + '-' + value.substring(7, 12);

        input.value = formatted;
        const hiddenInput = document.getElementById('telepon_hidden');
        hiddenInput.value = value.length > 0 ? '+62 ' + formatted : '';
        
        document.getElementById('prev_telepon').textContent = 'WhatsApp. ' + (hiddenInput.value || '+62 8123456789');
    }

    document.addEventListener("DOMContentLoaded", function() {
        const bindText = (inputId, prevId, defaultText, prefix = '') => {
            const el = document.getElementById(inputId);
            if(el) {
                el.addEventListener('input', function() {
                    document.getElementById(prevId).textContent = this.value ? prefix + this.value : defaultText;
                });
            }
        };

        bindText('input_nama_pt', 'prev_nama', 'NAMA PERUSAHAAN');
        bindText('input_alamat', 'prev_alamat', 'Alamat Perusahaan...');
        bindText('input_email', 'prev_email', 'Email: email@domain.com', 'Email: ');
        bindText('input_website', 'prev_website', 'Website. www.website.com', 'Website. ');

        document.getElementById('logo').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                document.getElementById('file-name').textContent = file.name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('prev_logo');
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

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

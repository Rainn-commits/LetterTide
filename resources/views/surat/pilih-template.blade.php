@extends('layouts.app')

@section('page-title', 'Buat Surat Baru')

@section('page-css')
    @vite('resources/css/surat.css')
@endsection

@section('content')

    <div class="surat-search">
        <div class="surat-search__field">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/>
                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            <input type="text" id="templateSearch" placeholder="Search">
        </div>
    </div>

    <div class="wizard-step">
        <span class="wizard-step__badge">1</span>
        <h2 class="wizard-step__title">Pilih Template</h2>
    </div>

    <div class="template-grid" style="display: grid !important; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important; gap: 20px !important;">
        
        @forelse ($templates as $template)
            <a href="{{ route('surat.form', $template->id) }}"
               class="template-card-fixed"
               data-name="{{ strtolower($template->nama_template) }}"
               style="display: flex !important; flex-direction: column !important; background: #ffffff !important; border-radius: 12px !important; overflow: hidden !important; text-decoration: none !important; box-shadow: 0 4px 12px rgba(0,0,0,0.06) !important; border: 1px solid #e2e8f0 !important;">
                
                <div style="background: #e6f7f7 !important; height: 160px !important; width: 100% !important; display: flex !important; align-items: center !important; justify-content: center !important; padding: 0 !important; margin: 0 !important;">
                    <div style="width: 80px !important; height: 110px !important; background: #ffffff !important; border-radius: 6px !important; padding: 10px 8px !important; box-shadow: 0 4px 10px rgba(0,0,0,0.08) !important; position: relative !important; display: flex !important; flex-direction: column !important; gap: 5px !important; box-sizing: border-box !important;">
                        <div style="height: 4px !important; background: #00C3D0 !important; border-radius: 2px !important; margin-bottom: 4px !important; width: 100% !important;"></div>
                        <div style="height: 3px !important; background: #cbd5e1 !important; border-radius: 2px !important; width: 80% !important;"></div>
                        <div style="height: 3px !important; background: #cbd5e1 !important; border-radius: 2px !important; width: 100% !important;"></div>
                        <div style="height: 3px !important; background: #cbd5e1 !important; border-radius: 2px !important; width: 60% !important;"></div>
                        <span style="position: absolute !important; bottom: 6px !important; right: 6px !important; font-size: 8px !important; font-weight: 700 !important; color: #00C3D0 !important; background: #e6f7f7 !important; padding: 2px 4px !important; border-radius: 3px !important;">DOC</span>
                    </div>
                </div>

                <div style="padding: 12px !important; text-align: center !important; font-weight: 600 !important; font-size: 13px !important; color: #1e293b !important; background: #ffffff !important; width: 100% !important; box-sizing: border-box !important; margin: 0 !important;">
                    {{ $template->nama_template }}
                </div>
            </a>
        @empty
            <div class="template-grid__empty">
                Belum ada template aktif. Hubungi Admin untuk menambahkan template surat.
            </div>
        @endforelse

        <div class="template-grid__empty" id="templateGridEmpty" style="display: none;">
            Template tidak ditemukan.
        </div>
    </div>

@endsection

@section('page-js')
    @vite('resources/js/surat-wizard.js')
@endsection

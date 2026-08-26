@extends('layouts.app')

@section('page-title', 'Pengaturan Kop Surat')

@section('page-css')
    @vite('resources/css/kop-surat.css')
@endsection

@section('content')

    @if(session('success'))
        <div class="alert alert-success" style="padding: 12px 16px; background-color: #d1e7dd; color: #0f5132; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-bottom: 24px; padding: 20px; background: #ffffff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 16px; color: #0f172a;">Pengaturan Kop Surat</h2>

        @if (!($kopSuratAktif ?? null))
            <p style="color: #64748b; margin-bottom: 16px;">Kop surat belum diatur.</p>
            <a href="{{ route('kop-surat.create') }}" class="btn-dark" style="display: inline-block; text-decoration: none;">
                + Buat Kop Surat Baru
            </a>
        @else
            <div class="kop-preview-card" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; background-color: #f8fafc; margin-bottom: 16px;">
                <div style="font-size: 0.85rem; font-weight: 700; color: #0d9488; text-transform: uppercase; margin-bottom: 6px;">
                    Versi {{ $kopSuratAktif->versi }} (Aktif)
                </div>
                <p style="margin: 4px 0; font-size: 1rem;"><strong>{{ $kopSuratAktif->nama_pt }}</strong></p>
                <p style="margin: 4px 0; color: #475569; font-size: 0.9rem;">{{ $kopSuratAktif->alamat_pt }}</p>
                <p style="margin: 4px 0; color: #475569; font-size: 0.9rem;">Telp: {{ $kopSuratAktif->telepon }} | Email: {{ $kopSuratAktif->email }}</p>
            </div>

            <a href="{{ route('kop-surat.create') }}" class="btn-dark" style="display: inline-block; text-decoration: none;">
                Ubah / Simpan Versi Baru
            </a>
        @endif
    </div>

    <div class="card" style="padding: 20px; background: #ffffff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px; color: #0f172a;">Riwayat Versi</h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 0.9rem;">
                        <th style="padding: 12px 16px; width: 140px;">Versi</th>
                        <th style="padding: 12px 16px;">Nama Perusahaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($riwayat ?? []) as $r)
                        <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;">
                            <td style="padding: 12px 16px; font-weight: 600; color: #0f172a;">Versi {{ $r->versi }}</td>
                            <td style="padding: 12px 16px; color: #334155;">{{ $r->nama_pt }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding: 16px; text-align: center; color: #94a3b8; font-size: 0.9rem;">
                                Belum ada riwayat versi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

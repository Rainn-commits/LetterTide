@extends('layouts.app')

@section('page-title', 'Pengaturan Kop Surat')

@section('page-css')
    @vite('resources/css/kop-surat.css')
@endsection

@section('content')

    @if(session('success'))
        <div style="padding: 14px 18px; background-color: #E6F9F0; color: #0B8A4A; border: 1px solid rgba(11,138,74,0.25); border-radius: 8px; margin-bottom: 20px; font-weight: 600; animation: fadeSlideUp 0.3s ease;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-bottom: 24px; padding: 24px; background: #ffffff; border-radius: 16px; box-shadow: 0 20px 50px -20px rgba(16,24,32,0.25);">
        <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 16px; color: #0f172a;">Pengaturan Kop Surat</h2>

        @if (!($kopSuratAktif ?? null))
            <p style="color: #64748b; margin-bottom: 16px;">Kop surat belum diatur.</p>
            <a href="{{ route('kop-surat.create') }}" style="display: inline-flex; align-items: center; padding: 11px 22px; background: linear-gradient(135deg, #00C3D0 0%, #0090A0 100%); color: white; border-radius: 999px; text-decoration: none; font-weight: 700; box-shadow: 0 6px 16px -4px rgba(0,195,208,0.45); transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                + Buat Kop Surat Baru
            </a>
        @else
            <div style="border: 1px solid rgba(0,195,208,0.25); border-radius: 12px; padding: 18px; background-color: #F0FDFE; margin-bottom: 16px;">
                <div style="font-size: 0.85rem; font-weight: 700; color: #0090A0; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 6px;">
                    Versi {{ $kopSuratAktif->versi }} (Aktif)
                </div>
                <p style="margin: 4px 0; font-size: 1rem;"><strong>{{ $kopSuratAktif->nama_pt }}</strong></p>
                <p style="margin: 4px 0; color: #475569; font-size: 0.9rem;">{{ $kopSuratAktif->alamat_pt }}</p>
                <p style="margin: 4px 0; color: #475569; font-size: 0.9rem;">Telp: {{ $kopSuratAktif->telepon }} | Email: {{ $kopSuratAktif->email }}</p>
            </div>

            <a href="{{ route('kop-surat.create') }}" style="display: inline-flex; align-items: center; padding: 11px 22px; background: linear-gradient(135deg, #00C3D0 0%, #0090A0 100%); color: white; border-radius: 999px; text-decoration: none; font-weight: 700; box-shadow: 0 6px 16px -4px rgba(0,195,208,0.45); transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                Ubah / Simpan Versi Baru
            </a>
        @endif
    </div>

    <div class="card" style="padding: 24px; background: #ffffff; border-radius: 16px; box-shadow: 0 20px 50px -20px rgba(16,24,32,0.25);">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; color: #0f172a;">Riwayat Versi</h3>

        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #E6FBFC; color: #0090A0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.03em;">
                        <th style="padding: 12px 16px; width: 140px; white-space: nowrap;">Versi</th>
                        <th style="padding: 12px 16px; white-space: nowrap;">Nama Perusahaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($riwayat ?? []) as $r)
                        <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;">
                            <td style="padding: 12px 16px; font-weight: 700; color: #0f172a; white-space: nowrap;">Versi {{ $r->versi }}</td>
                            <td style="padding: 12px 16px; color: #334155;">{{ $r->nama_pt }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding: 24px 16px; text-align: center; color: #94a3b8; font-size: 0.9rem;">
                                Belum ada riwayat versi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
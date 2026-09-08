@extends('layouts.app')

@section('page-title', 'Pengaturan Kop Surat')

@section('page-css')
    @vite('resources/css/kop-surat.css')
@endsection

@section('content')

    @if(session('success'))
        <div style="padding: 12px 16px; background-color: #E6F9F0; color: #0B8A4A; border: 1px solid rgba(11,138,74,0.25); border-radius: 8px; margin-bottom: 12px; font-weight: 600; animation: fadeSlideUp 0.3s ease;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="width: 60%; max-width: 1250px; margin: 0 auto 10px; padding: 20px 20px; background: #ffffff; border-radius: 16px; box-shadow: 0 8px 22px -14px rgba(16,24,32,0.25);">
        <h2 style="font-weight: 600; margin: 0 0 8px; color: #0f172a;">Pengaturan Kop Surat</h2>

        @if (!($kopSuratAktif ?? null))
            <a href="{{ route('kop-surat.create') }}" style="display: flex; align-items: center; justify-content: center; padding: 12px 18px; background: linear-gradient(135deg, #00C3D0 0%, #009ea8 100%); color: white; border-radius: 999px; text-decoration: none; font-weight: 700; box-shadow: 0 5px 12px -4px rgba(0,195,208,0.45); transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                + Buat Kop Surat Baru
            </a>
        @else
            <div style="border: 1px solid rgba(0,195,208,0.25); border-radius: 12px; padding: 14px 16px; background-color: #F0FDFE; margin-bottom: 12px;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #0090A0; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 4px;">
                    Versi {{ $kopSuratAktif->versi }} (Aktif)
                </div>
                <p style="margin: 3px 0; font-size: 0.95rem;"><strong>{{ $kopSuratAktif->nama_pt }}</strong></p>
                <p style="margin: 3px 0; color: #475569; font-size: 0.85rem;">{{ $kopSuratAktif->alamat_pt }}</p>
                <p style="margin: 3px 0; color: #475569; font-size: 0.85rem;">Telp: {{ $kopSuratAktif->telepon }} | Email: {{ $kopSuratAktif->email }}</p>
            </div>

            <a href="{{ route('kop-surat.preview', $kopSuratAktif) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; background: #ffffff; color: #0090A0; border: 1.5px solid rgba(0,195,208,0.4); border-radius: 999px; text-decoration: none; font-weight: 700; margin-right: 8px; transition: all 0.15s ease;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                Preview
            </a>

            <a href="{{ route('kop-surat.create') }}" style="display: inline-flex; align-items: center; padding: 9px 20px; background: linear-gradient(135deg, #00C3D0 0%, #0090A0 100%); color: white; border-radius: 999px; text-decoration: none; font-weight: 700; box-shadow: 0 5px 14px -4px rgba(0,195,208,0.45); transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                Ubah / Simpan Versi Baru
            </a>
        @endif
    </div>

    <div class="card" style="width: 60%; max-width: 1250px; margin: 0 auto; padding: 30px 20px; background: #ffffff; border-radius: 16px; box-shadow: 0 8px 22px -14px rgba(16,24,32,0.25);">
        <h3 style="font-weight: 700; margin: 0 0 8px; color: #0f172a;">Riwayat Versi</h3>

        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #E6FBFC; color: #0090A0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.03em;">
                        <th style="padding: 8px 14px; width: 140px; white-space: nowrap;">Versi</th>
                        <th style="padding: 9px 14px 9px 40px; white-space: nowrap;">Nama Perusahaan</th>
                        <th style="padding: 8px 14px; width: 100px; text-align: center; white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($riwayat ?? []) as $r)
                    <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;">
                        <td style="padding: 8px 14px; font-weight: 700; color: #0f172a; white-space: nowrap;">Versi {{ $r->versi }}</td>
                        <td style="padding: 9px 14px 9px 40px; color: #334155;">{{ $r->nama_pt }}</td>
                        <td style="padding: 8px 14px; text-align: center;">
                            <a href="{{ route('kop-surat.preview', $r) }}" title="Lihat Preview" style="display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; background: #E6FBFC; color: #0090A0; text-decoration: none;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding: 20px 16px; text-align: center; color: #94a3b8; font-size: 0.9rem;">
                                Belum ada riwayat versi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

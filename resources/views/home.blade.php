@extends('layouts.app')

@section('title', 'LetterFlow - Beranda')

@section('content')
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:1.5rem">
    <h1>LetterFlow</h1>
    <p>Sistem Manajemen Administrasi Persuratan | PT Inovindo Digital Media</p>
    <a href="{{ route('login') }}">Masuk ke Sistem</a>
</div>
@endsection

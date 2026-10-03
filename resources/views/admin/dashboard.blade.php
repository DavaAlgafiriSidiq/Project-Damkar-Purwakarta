@extends('layouts.app')

@section('content')
<div class="container-xxl py-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Panel Validasi Admin</h3>
        </div>
        <div class="card-body">
            <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Anda login sebagai Admin.</p>
            <p>Di halaman ini Anda dapat memvalidasi data laporan dari petugas lapangan.</p>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-xxl py-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Panel Input Petugas</h3>
        </div>
        <div class="card-body">
            <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Anda login sebagai Petugas Lapangan.</p>
            <p>Di halaman ini Anda dapat mencatat laporan kejadian kebakaran dan operasi rescue baru.</p>
        </div>
    </div>
</div>
@endsection
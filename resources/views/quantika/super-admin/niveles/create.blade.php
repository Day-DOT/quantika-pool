@extends('quantika.super-admin.layout')

@section('title', 'Nuevo nivel')
@section('page-title', 'Nuevo nivel de natación')

@section('content')

    <a href="{{ route('niveles.index') }}" class="breadcrumb-back">← Volver a niveles</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.niveles.store') }}" enctype="multipart/form-data">
            @csrf
            @include('quantika.super-admin.niveles._form', ['nivel' => null])
        </form>
    </div>

@endsection

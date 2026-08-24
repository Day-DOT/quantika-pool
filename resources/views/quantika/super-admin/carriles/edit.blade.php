@extends('quantika.super-admin.layout')

@section('title', 'Editar carril')
@section('page-title', 'Editar carril')

@section('content')

    <a href="{{ route('super-admin.carriles.index') }}" class="breadcrumb-back">← Volver a carriles</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.carriles.update', $carril) }}">
            @csrf
            @method('PUT')
            @include('quantika.super-admin.carriles._form', ['carril' => $carril])
        </form>
    </div>

@endsection

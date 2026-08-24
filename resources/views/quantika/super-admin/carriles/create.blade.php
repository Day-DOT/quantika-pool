@extends('quantika.super-admin.layout')

@section('title', 'Nuevo carril')
@section('page-title', 'Nuevo carril')

@section('content')

    <a href="{{ route('super-admin.carriles.index') }}" class="breadcrumb-back">← Volver a carriles</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.carriles.store') }}">
            @csrf
            @include('quantika.super-admin.carriles._form', ['carril' => null])
        </form>
    </div>

@endsection

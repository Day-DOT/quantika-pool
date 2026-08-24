@extends('quantika.super-admin.layout')

@section('title', 'Nuevo usuario')
@section('page-title', 'Nuevo usuario')

@section('content')

    <a href="{{ route('super-admin.usuarios.index') }}" class="breadcrumb-back">← Volver a usuarios</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.usuarios.store') }}">
            @csrf
            @include('quantika.super-admin.usuarios._form', ['usuario' => null])
        </form>
    </div>

@endsection

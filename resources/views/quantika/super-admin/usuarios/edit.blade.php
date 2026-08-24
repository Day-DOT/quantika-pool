@extends('quantika.super-admin.layout')

@section('title', 'Editar usuario')
@section('page-title', 'Editar usuario')

@section('content')

    <a href="{{ route('super-admin.usuarios.index') }}" class="breadcrumb-back">← Volver a usuarios</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')
            @include('quantika.super-admin.usuarios._form', ['usuario' => $usuario])
        </form>
    </div>

@endsection

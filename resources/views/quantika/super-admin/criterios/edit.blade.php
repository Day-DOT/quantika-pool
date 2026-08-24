@extends('quantika.super-admin.layout')

@section('title', 'Editar criterio')
@section('page-title', 'Editar criterio de evaluación')

@section('content')

    <a href="{{ route('super-admin.criterios.index') }}" class="breadcrumb-back">← Volver a criterios</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.criterios.update', $criterio) }}">
            @csrf
            @method('PUT')
            @include('quantika.super-admin.criterios._form', ['criterio' => $criterio])
        </form>
    </div>

@endsection

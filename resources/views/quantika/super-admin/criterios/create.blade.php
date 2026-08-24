@extends('quantika.super-admin.layout')

@section('title', 'Nuevo criterio')
@section('page-title', 'Nuevo criterio de evaluación')

@section('content')

    <a href="{{ route('super-admin.criterios.index') }}" class="breadcrumb-back">← Volver a criterios</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.criterios.store') }}">
            @csrf
            @include('quantika.super-admin.criterios._form', ['criterio' => null])
        </form>
    </div>

@endsection

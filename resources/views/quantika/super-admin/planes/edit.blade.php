@extends('quantika.super-admin.layout')

@section('title', 'Editar plan')
@section('page-title', 'Editar plan')

@section('content')

    <a href="{{ route('super-admin.planes.index') }}" class="breadcrumb-back">← Volver a planes</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.planes.update', $plan) }}">
            @csrf
            @method('PUT')
            @include('quantika.super-admin.planes._form', ['plan' => $plan])
        </form>
    </div>

@endsection

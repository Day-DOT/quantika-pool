@extends('quantika.super-admin.layout')

@section('title', 'Nuevo plan')
@section('page-title', 'Nuevo plan')

@section('content')

    <a href="{{ route('super-admin.planes.index') }}" class="breadcrumb-back">← Volver a planes</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.planes.store') }}">
            @csrf
            @include('quantika.super-admin.planes._form', ['plan' => null])
        </form>
    </div>

@endsection

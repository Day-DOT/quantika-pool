@extends('quantika.super-admin.layout')

@section('title', 'Editar nivel')
@section('page-title', 'Editar nivel')

@section('content')

    <a href="{{ route('niveles.index') }}" class="breadcrumb-back">← Volver a niveles</a>

    <div class="panel form-card">
        <form method="POST" action="{{ route('super-admin.niveles.update', $nivel) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('quantika.super-admin.niveles._form', ['nivel' => $nivel])
        </form>
    </div>

@endsection

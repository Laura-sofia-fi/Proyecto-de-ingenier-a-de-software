@extends('layouts.app')

@section('title', 'Editar producto')
@section('page-title', 'Editar producto')
@section('page-description', 'Actualiza datos del producto dentro del catálogo comercial')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}" class="d-grid gap-3">
            @csrf
            @method('PUT')
            @include('products._form')
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

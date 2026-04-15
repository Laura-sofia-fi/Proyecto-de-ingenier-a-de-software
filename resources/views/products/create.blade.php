@extends('layouts.app')

@section('title', 'Nuevo producto')
@section('page-title', 'Nuevo producto')
@section('page-description', 'Agrega productos al catálogo comercial de NATADINATTA')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}" class="d-grid gap-3">
            @csrf
            @include('products._form')
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Guardar producto</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

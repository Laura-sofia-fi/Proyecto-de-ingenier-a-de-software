@extends('layouts.app')

@section('title', 'Editar cliente')
@section('page-title', 'Editar cliente')
@section('page-description', 'Actualiza la información del cliente')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <form method="POST" action="{{ route('clients.update', $client) }}" class="d-grid gap-3">
            @csrf
            @method('PUT')
            @include('clients._form')
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

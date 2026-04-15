@extends('layouts.app')

@section('title', 'Nuevo cliente')
@section('page-title', 'Nuevo cliente')
@section('page-description', 'Crea un cliente para asociarlo a ventas y facturas')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <form method="POST" action="{{ route('clients.store') }}" class="d-grid gap-3">
            @csrf
            @include('clients._form')
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Guardar cliente</button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

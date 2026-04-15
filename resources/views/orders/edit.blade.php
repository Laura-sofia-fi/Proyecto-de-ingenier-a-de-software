@extends('layouts.app')

@section('title', 'Editar pedido')
@section('page-title', 'Editar pedido')
@section('page-description', 'Actualiza un pedido pendiente antes de confirmarlo')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <form method="POST" action="{{ route('orders.update', $order) }}" class="d-grid gap-3">
            @csrf
            @method('PUT')
            @include('orders._form')
            <div class="d-flex flex-column flex-sm-row gap-2">
                <button class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

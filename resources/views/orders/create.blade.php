@extends('layouts.app')

@section('title', 'Nueva venta')
@section('page-title', 'Nueva venta')
@section('page-description', 'Registra un pedido y sus productos')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <form method="POST" action="{{ route('orders.store') }}" class="d-grid gap-3">
            @csrf
            @include('orders._form')
            <div class="d-flex flex-column flex-sm-row gap-2">
                <button class="btn btn-primary">Guardar pedido</button>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

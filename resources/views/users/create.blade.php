@extends('layouts.app')

@section('title', 'Nuevo usuario')
@section('page-title', 'Nuevo usuario')
@section('page-description', 'Crea empleados o administradores del sistema')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}" class="d-grid gap-3">
            @csrf
            @include('users._form')
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Guardar usuario</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Editar usuario')
@section('page-title', 'Editar usuario')
@section('page-description', 'Actualiza permisos o credenciales del usuario')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}" class="d-grid gap-3">
            @csrf
            @method('PUT')
            @include('users._form')
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Configuración')
@section('page-title', 'Configuración de empresa')
@section('page-description', 'Datos usados para facturación de NATADINATTA')

@section('content')
<div class="card content-card">
    <div class="card-body">
        <form method="POST" action="{{ route('settings.update') }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">NIT</label>
                <input type="text" name="nit" class="form-control" value="{{ old('nit', $company->nit) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ciudad</label>
                <input type="text" name="city" class="form-control" value="{{ old('city', $company->city) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Dirección</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Prefijo factura</label>
                <input type="text" name="invoice_prefix" class="form-control" value="{{ old('invoice_prefix', $company->invoice_prefix) }}" required>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Guardar configuración</button>
            </div>
        </form>
    </div>
</div>
@endsection

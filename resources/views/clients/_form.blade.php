<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Documento</label>
        <input type="text" name="document_number" class="form-control" value="{{ old('document_number', $client->document_number) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Correo</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Ciudad</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $client->city) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Dirección</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $client->address) }}">
    </div>
    <div class="col-12">
        <label class="form-label">Notas</label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $client->notes) }}</textarea>
    </div>
</div>

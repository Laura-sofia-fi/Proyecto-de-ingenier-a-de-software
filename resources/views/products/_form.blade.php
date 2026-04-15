<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">SKU</label>
        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Precio base</label>
        <input type="number" min="0" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
    </div>
    <div class="col-md-8 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->exists ? $product->is_active : true))>
            <label class="form-check-label" for="is_active">Producto activo</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
    </div>
</div>

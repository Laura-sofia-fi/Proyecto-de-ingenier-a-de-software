@php($existingItems = old('items', $order->exists ? $order->items->map(fn($item) => ['product_id' => $item->product_id, 'product_name' => $item->product_name, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price])->toArray() : [['product_id' => '', 'product_name' => '', 'quantity' => 1, 'unit_price' => '']]))
@php($productCatalog = $products->map(fn($product) => ['id' => $product->id, 'name' => $product->name, 'price' => (float) $product->price])->values())

<style>
    .order-items-table th,
    .order-items-table td {
        vertical-align: middle;
    }
    @media (max-width: 767.98px) {
        .order-items-wrap {
            overflow: visible;
        }
        .order-items-table {
            min-width: 100%;
        }
        .order-items-table thead {
            display: none;
        }
        .order-items-table,
        .order-items-table tbody,
        .order-items-table tr,
        .order-items-table td {
            display: block;
            width: 100%;
        }
        .order-items-table tr {
            border: 1px solid #dbe5f2;
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8fbff;
        }
        .order-items-table td {
            border: 0;
            padding: 0;
            margin-bottom: .85rem;
        }
        .order-items-table td:last-child {
            margin-bottom: 0;
        }
        .order-items-table td::before {
            content: attr(data-label);
            display: block;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: .4rem;
        }
        .order-items-table td[data-label="Acciones"]::before {
            display: none;
        }
        .order-items-table .remove-row {
            width: 100%;
        }
    }
</style>

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label">Cliente</label>
        <select name="client_id" class="form-select" required>
            <option value="">Selecciona un cliente</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" @selected((int) old('client_id', $order->client_id) === $client->id)>{{ $client->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label">Fecha del pedido</label>
        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', optional($order->order_date)->format('Y-m-d') ?: now()->toDateString()) }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">Observaciones</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $order->notes) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Productos del pedido</label>
        <div class="table-responsive order-items-wrap">
            <table class="table align-middle order-items-table" id="items-table">
                <thead>
                <tr>
                    <th>Catálogo</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($existingItems as $index => $item)
                    <tr>
                        <td data-label="Catálogo">
                            <select name="items[{{ $index }}][product_id]" class="form-select product-select">
                                <option value="">Manual</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        @selected((int) ($item['product_id'] ?? 0) === $product->id)>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td data-label="Producto"><input type="text" name="items[{{ $index }}][product_name]" class="form-control" value="{{ $item['product_name'] }}" required></td>
                        <td data-label="Cantidad"><input type="number" min="1" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item['quantity'] }}" required></td>
                        <td data-label="Precio unitario"><input type="number" min="0" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control" value="{{ $item['unit_price'] }}" required></td>
                        <td data-label="Acciones"><button type="button" class="btn btn-outline-danger btn-sm remove-row">Quitar</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-outline-primary w-100 btn-sm mt-2" id="add-row">Agregar producto</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#items-table tbody');
    const addRowButton = document.getElementById('add-row');

    function rowTemplate(index) {
        return `<tr>
            <td data-label="Catálogo">
                <select name="items[${index}][product_id]" class="form-select product-select">
                    <option value="">Manual</option>
                    ${window.productCatalog.map(product => `<option value="${product.id}" data-name="${product.name}" data-price="${product.price}">${product.name}</option>`).join('')}
                </select>
            </td>
            <td data-label="Producto"><input type="text" name="items[${index}][product_name]" class="form-control" required></td>
            <td data-label="Cantidad"><input type="number" min="1" name="items[${index}][quantity]" class="form-control" value="1" required></td>
            <td data-label="Precio unitario"><input type="number" min="0" step="0.01" name="items[${index}][unit_price]" class="form-control" value="0" required></td>
            <td data-label="Acciones"><button type="button" class="btn btn-outline-danger btn-sm remove-row">Quitar</button></td>
        </tr>`;
    }

    window.productCatalog = @json($productCatalog);

    addRowButton?.addEventListener('click', function () {
        const index = tableBody.querySelectorAll('tr').length;
        tableBody.insertAdjacentHTML('beforeend', rowTemplate(index));
    });

    tableBody?.addEventListener('change', function (event) {
        if (!event.target.classList.contains('product-select')) {
            return;
        }

        const row = event.target.closest('tr');
        const selected = event.target.selectedOptions[0];
        const nameInput = row.querySelector('input[name*="[product_name]"]');
        const priceInput = row.querySelector('input[name*="[unit_price]"]');

        if (event.target.value) {
            nameInput.value = selected.dataset.name || '';
            priceInput.value = selected.dataset.price || 0;
        }
    });

    tableBody?.addEventListener('click', function (event) {
        if (!event.target.classList.contains('remove-row')) {
            return;
        }

        if (tableBody.querySelectorAll('tr').length > 1) {
            event.target.closest('tr').remove();
        }
    });
});
</script>

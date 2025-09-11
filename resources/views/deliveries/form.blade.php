<div class="form-group mb-3">
    <label for="order_id">Commande</label>
    <select name="order_id" id="order_id" class="form-control" required>
        @foreach ($orders as $order)
            <option value="{{ $order->id }}" @selected(old('order_id', $delivery->order_id ?? '') == $order->id)>
                Commande #{{ $order->id }}
            </option>
        @endforeach
    </select>
    @error('order_id') <span class="text-danger">{{ $message }}</span> @enderror
</div>
<div class="form-group mb-3">
    <label for="delivery_route_id">Tournée de Livraison</label>
    <select name="delivery_route_id" id="delivery_route_id" class="form-control" required>
        @foreach ($deliveryRoutes as $route)
            <option value="{{ $route->id }}" @selected(old('delivery_route_id', $delivery->delivery_route_id ?? '') == $route->id)>
                Tournée du {{ $route->delivery_date->format('d/m/Y') }}
            </option>
        @endforeach
    </select>
    @error('delivery_route_id') <span class="text-danger">{{ $message }}</span> @enderror
</div>
<div class="form-group mb-3">
    <label for="status">Statut</label>
    <select name="status" id="status" class="form-control" required>
        <option value="En cours" @selected(old('status', $delivery->status ?? '') == 'En cours')>En cours</option>
        <option value="Terminée" @selected(old('status', $delivery->status ?? '') == 'Terminée')>Terminée</option>
        <option value="Annulée" @selected(old('status', $delivery->status ?? '') == 'Annulée')>Annulée</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
</div>
<div class="form-group mb-3">
    <label for="notes">Notes</label>
    <textarea name="notes" id="notes" class="form-control">{{ old('notes', $delivery->notes ?? '') }}</textarea>
    @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
</div>
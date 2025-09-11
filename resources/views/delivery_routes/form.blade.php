<div class="form-group mb-3">
    <label for="delivery_date">Date de la tournée</label>
    <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date', $deliveryRoute->delivery_date ?? '') }}" required>
    @error('delivery_date') <span class="text-danger">{{ $message }}</span> @enderror
</div>
<div class="form-group mb-3">
    <label for="driver_id">Chauffeur</label>
    <select name="driver_id" id="driver_id" class="form-control" required>
        @foreach ($drivers as $driver)
            <option value="{{ $driver->id }}" @selected(old('driver_id', $deliveryRoute->driver_id ?? '') == $driver->id)>
                {{ $driver->name }}
            </option>
        @endforeach
    </select>
    @error('driver_id') <span class="text-danger">{{ $message }}</span> @enderror
</div>
<div class="form-group mb-3">
    <label for="vehicle_info">Informations sur le véhicule</label>
    <input type="text" name="vehicle_info" id="vehicle_info" class="form-control" value="{{ old('vehicle_info', $deliveryRoute->vehicle_info ?? '') }}">
    @error('vehicle_info') <span class="text-danger">{{ $message }}</span> @enderror
</div>
<div class="form-group mb-3">
    <label for="status">Statut</label>
    <select name="status" id="status" class="form-control" required>
        <option value="Planifiée" @selected(old('status', $deliveryRoute->status ?? '') == 'Planifiée')>Planifiée</option>
        <option value="En cours" @selected(old('status', $deliveryRoute->status ?? '') == 'En cours')>En cours</option>
        <option value="Terminée" @selected(old('status', $deliveryRoute->status ?? '') == 'Terminée')>Terminée</option>
        <option value="Annulée" @selected(old('status', $deliveryRoute->status ?? '') == 'Annulée')>Annulée</option>
    </select>
    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
</div>
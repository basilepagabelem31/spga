<!-- <div class="modal fade" id="editPartnerModal{{ $partner->id }}" tabindex="-1" aria-labelledby="editPartnerModalLabel{{ $partner->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPartnerModalLabel{{ $partner->id }}">Modifier le partenaire : {{ $partner->establishment_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('partners.update', $partner) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="establishment_name" class="form-label">Nom de l'établissement <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="establishment_name" name="establishment_name" value="{{ old('establishment_name', $partner->establishment_name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_name" class="form-label">Nom du contact</label>
                            <input type="text" class="form-control" id="contact_name" name="contact_name" value="{{ old('contact_name', $partner->contact_name) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="function" class="form-label">Fonction du contact</label>
                            <input type="text" class="form-control" id="function" name="function" value="{{ old('function', $partner->function) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $partner->phone) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $partner->email) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="locality_region" class="form-label">Localité / Région</label>
                            <input type="text" class="form-control" id="locality_region" name="locality_region" value="{{ old('locality_region', $partner->locality_region) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type de partenaire <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="Producteur individuel" {{ old('type', $partner->type) == 'Producteur individuel' ? 'selected' : '' }}>Producteur individuel</option>
                                <option value="Coopérative agricole/maraîchère" {{ old('type', $partner->type) == 'Coopérative agricole/maraîchère' ? 'selected' : '' }}>Coopérative agricole/maraîchère</option>
                                <option value="Ferme partenaire" {{ old('type', $partner->type) == 'Ferme partenaire' ? 'selected' : '' }}>Ferme partenaire</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="years_of_experience" class="form-label">Années d'expérience</label>
                            <input type="number" class="form-control" id="years_of_experience" name="years_of_experience" value="{{ old('years_of_experience', $partner->years_of_experience) }}" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Associer à un utilisateur existant</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">Aucun utilisateur</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $partner->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">L'utilisateur associé doit avoir le rôle 'partenaire'.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

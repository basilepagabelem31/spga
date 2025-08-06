<div class="modal fade" id="createPartnerModal" tabindex="-1" aria-labelledby="createPartnerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPartnerModalLabel">Ajouter un nouveau partenaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('partners.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="establishment_name" class="form-label">Nom de l'établissement <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="establishment_name" name="establishment_name" value="{{ old('establishment_name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_name" class="form-label">Nom du contact</label>
                            <input type="text" class="form-control" id="contact_name" name="contact_name" value="{{ old('contact_name') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="function" class="form-label">Fonction du contact</label>
                            <input type="text" class="form-control" id="function" name="function" value="{{ old('function') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="locality_region" class="form-label">Localité / Région</label>
                            <input type="text" class="form-control" id="locality_region" name="locality_region" value="{{ old('locality_region') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type de partenaire <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="Producteur individuel" {{ old('type') == 'Producteur individuel' ? 'selected' : '' }}>Producteur individuel</option>
                                <option value="Coopérative agricole/maraîchère" {{ old('type') == 'Coopérative agricole/maraîchère' ? 'selected' : '' }}>Coopérative agricole/maraîchère</option>
                                <option value="Ferme partenaire" {{ old('type') == 'Ferme partenaire' ? 'selected' : '' }}>Ferme partenaire</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="years_of_experience" class="form-label">Années d'expérience</label>
                            <input type="number" class="form-control" id="years_of_experience" name="years_of_experience" value="{{ old('years_of_experience') }}" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="user_id_create" class="form-label">Associer à un utilisateur partenaire existant</label>
                        <select class="form-select" id="user_id_create" name="user_id">
                            <option value="">Sélectionner un utilisateur</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Seuls les utilisateurs avec le rôle 'partenaire' sont affichés ici.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le partenaire</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Inclure Select2 CSS et JS --}}
{{-- Il est recommandé de les inclure dans votre layout principal si Select2 est utilisé à plusieurs endroits --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Initialiser Select2 lorsque la modale est affichée
    document.addEventListener('DOMContentLoaded', function() {
        var createModal = document.getElementById('createPartnerModal');
        createModal.addEventListener('shown.bs.modal', function () {
            $('#user_id_create').select2({
                dropdownParent: $('#createPartnerModal') // Important pour que Select2 s'affiche correctement dans une modale Bootstrap
            });
        });

        // Si la modale est déjà ouverte au chargement de la page (ex: validation échouée)
        if ($(createModal).hasClass('show')) {
            $('#user_id_create').select2({
                dropdownParent: $('#createPartnerModal')
            });
        }
    });
</script>

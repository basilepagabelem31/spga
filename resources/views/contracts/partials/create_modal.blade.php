<div class="modal fade" id="createContractModal" tabindex="-1" aria-labelledby="createContractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createContractModalLabel">Ajouter un nouveau contrat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data"> {{-- TRÈS IMPORTANT : enctype="multipart/form-data" --}}
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="partner_id_create" class="form-label">Partenaire <span class="text-danger">*</span></label>
                        <select class="form-select" id="partner_id_create" name="partner_id" required>
                            <option value="">Sélectionner un partenaire</option>
                            @foreach ($partners as $partner)
                                <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->establishment_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du contrat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Date de début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}">
                            <small class="form-text text-muted">Laisser vide pour un contrat illimité.</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="contract_file_create" class="form-label">Fichier du contrat</label>
                        <input type="file" class="form-control" id="contract_file_create" name="contract_file"> {{-- CHANGEMENT ICI : type="file" --}}
                        <small class="form-text text-muted">Formats acceptés : PDF, Word (doc/docx), Images (jpg, jpeg, png). Taille max : 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le contrat</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Inclure Select2 CSS et JS (si non déjà dans le layout principal) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var createModal = document.getElementById('createContractModal');
        createModal.addEventListener('shown.bs.modal', function () {
            $('#partner_id_create').select2({
                dropdownParent: $('#createContractModal')
            });
        });

        if ($(createModal).hasClass('show')) {
            $('#partner_id_create').select2({
                dropdownParent: $('#createContractModal')
            });
        }
    });
</script>

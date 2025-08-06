<div class="modal fade" id="editContractModal{{ $contract->id }}" tabindex="-1" aria-labelledby="editContractModalLabel{{ $contract->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editContractModalLabel{{ $contract->id }}">Modifier le contrat : {{ $contract->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('contracts.update', $contract) }}" method="POST" enctype="multipart/form-data"> {{-- TRÈS IMPORTANT : enctype="multipart/form-data" --}}
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="partner_id_edit_{{ $contract->id }}" class="form-label">Partenaire <span class="text-danger">*</span></label>
                        <select class="form-select" id="partner_id_edit_{{ $contract->id }}" name="partner_id" required>
                            <option value="">Sélectionner un partenaire</option>
                            @foreach ($partners as $partner)
                                <option value="{{ $partner->id }}" @selected(old('partner_id', $contract->partner_id) == $partner->id)>
                                    {{ $partner->establishment_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du contrat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $contract->title) }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Date de début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $contract->end_date ? $contract->end_date->format('Y-m-d') : '') }}">
                            <small class="form-text text-muted">Laisser vide pour un contrat illimité.</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $contract->description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="contract_file_edit_{{ $contract->id }}" class="form-label">Fichier du contrat</label>
                        <input type="file" class="form-control" id="contract_file_edit_{{ $contract->id }}" name="contract_file"> {{-- CHANGEMENT ICI : type="file" --}}
                        <small class="form-text text-muted">Formats acceptés : PDF, Word (doc/docx), Images (jpg, jpeg, png). Taille max : 2MB.</small>
                        
                        @if ($contract->file_path)
                            <div class="mt-2">
                                <small class="form-text text-muted">Fichier actuel : 
                                    <a href="{{ Storage::url($contract->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info p-1 py-0" title="Voir le fichier actuel">
                                        <i class="fas fa-eye me-1"></i> Voir
                                    </a>
                                </small>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="clear_file" id="clear_file_{{ $contract->id }}" value="1">
                                    <label class="form-check-label" for="clear_file_{{ $contract->id }}">
                                        Supprimer le fichier actuel
                                    </label>
                                </div>
                            </div>
                        @else
                            <small class="form-text text-muted">Aucun fichier n'est actuellement associé à ce contrat.</small>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Mettre à jour</button>
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
        var editModal{{ $contract->id }} = document.getElementById('editContractModal{{ $contract->id }}');
        editModal{{ $contract->id }}.addEventListener('shown.bs.modal', function () {
            $('#partner_id_edit_{{ $contract->id }}').select2({
                dropdownParent: $('#editContractModal{{ $contract->id }}')
            });
        });

        if ($(editModal{{ $contract->id }}).hasClass('show')) {
            $('#partner_id_edit_{{ $contract->id }}').select2({
                dropdownParent: $('#editContractModal{{ $contract->id }}')
            });
        }
    });
</script>

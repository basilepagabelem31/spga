@extends('layouts.app') {{-- Assurez-vous d'avoir un layout principal --}}

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">📋 Suivis de Production</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createProductionFollowUpModal">
            <i class="fas fa-plus-circle me-1"></i> Ajouter un suivi
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- Section de filtrage --}}
    <form action="{{ route('production_follow_ups.index') }}" method="GET" class="mb-4 p-4 bg-white rounded-4 shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Site, producteur, ou culture..." value="{{ request('search') }}">
            </div>
            <div class="col-md-5">
                <label for="production_type" class="form-label">Type de production</label>
                <select class="form-select" id="production_type" name="production_type">
                    <option value="">Tous</option>
                    @foreach ($productionTypes as $type)
                        <option value="{{ $type->production_type }}" @if(request('production_type') == $type->production_type) selected @endif>
                            {{ $type->production_type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid gap-2">
                <button type="submit" class="btn btn-info"><i class="fas fa-filter me-1"></i> Filtrer</button>
                <a href="{{ route('production_follow_ups.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Site de production</th>
                            <th>Producteur</th>
                            <th>Culture</th>
                            <th>Date de suivi</th>
                            <th>Type de production</th>
                            <th>Signature</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productionFollowUps as $followUp)
                            <tr>
                                <td>{{ $followUp->production_site }}</td>
                                <td class="{{ $followUp->producer_name ? '' : 'text-not-set' }}">{{ $followUp->producer_name ?? 'Non renseigné' }}</td>
                                <td>{{ $followUp->culture_name }}</td>
                                <td>{{ $followUp->follow_up_date->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = '';
                                        switch ($followUp->production_type) {
                                            case 'Conventionnel':
                                                $badgeClass = 'bg-primary';
                                                break;
                                            case 'Biologique':
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'Agroécologie':
                                                $badgeClass = 'bg-info';
                                                break;
                                            default:
                                                $badgeClass = 'bg-secondary';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $followUp->production_type }}</span>
                                </td>

 <td>
                @if ($followUp->responsible_signature)
                    <img src="{{ $followUp->responsible_signature }}" alt="Signature du responsable" style="max-height: 50px; width: auto; border: 1px solid #ddd; border-radius: 4px;">
                @else
                    <span class="text-muted">Non signé</span>
                @endif
            </td>



                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('production_follow_ups.show', $followUp) }}" class="btn btn-sm btn-outline-secondary" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editFollowUpModal{{ $followUp->id }}" title="Modifier le suivi">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteFollowUpModal{{ $followUp->id }}" title="Supprimer le suivi">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modales pour chaque suivi --}}
                            @include('production_follow_ups.partials.edit_modal', ['followUp' => $followUp])
                            @include('production_follow_ups.partials.delete_modal', ['followUp' => $followUp])

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucun suivi de production trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $productionFollowUps->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

{{-- Modale d'ajout de suivi --}}
@include('production_follow_ups.partials.create_modal')

{{-- Script pour initialiser les tooltips Bootstrap --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Fonction générique pour configurer un pad de signature dans une modale
        function setupSignaturePad(modal) {
            const canvas = modal.querySelector('.signature-pad');
            const clearButton = modal.querySelector('button[id^="clear-signature"]');
            const outputInput = modal.querySelector('input[name="responsible_signature"]');
            
            if (!canvas || !clearButton || !outputInput) {
                return;
            }

            const signaturePad = new SignaturePad(canvas);
            
            // Gérer le redimensionnement et le chargement de la signature à l'ouverture de la modale
            modal.addEventListener('shown.bs.modal', () => {
                // Redimensionner le canvas pour qu'il corresponde à la taille de son conteneur
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width;
                canvas.height = rect.height;
                
                // Charger la signature existante pour les modales d'édition si elle existe
                const existingSignatureDataElement = modal.querySelector('.signature-data');
                const existingSignatureData = existingSignatureDataElement ? existingSignatureDataElement.dataset.signature : null;
                
                signaturePad.clear();
                if (existingSignatureData) {
                    try {
                        signaturePad.fromDataURL(existingSignatureData);
                    } catch (error) {
                        console.error('Erreur lors du chargement de la signature existante:', error);
                    }
                }
            });

            // Gérer le bouton "Effacer"
            clearButton.addEventListener('click', () => {
                signaturePad.clear();
                outputInput.value = '';
            });

            // Gérer la soumission du formulaire
            const form = modal.querySelector('form');
            form.addEventListener('submit', function () {
                if (!signaturePad.isEmpty()) {
                    outputInput.value = signaturePad.toDataURL();
                } else {
                    // Pour l'édition, conserver la signature existante si le pad n'est pas utilisé
                    const existingSignatureDataElement = modal.querySelector('.signature-data');
                    const existingSignatureData = existingSignatureDataElement ? existingSignatureDataElement.dataset.signature : null;
                    outputInput.value = existingSignatureData || '';
                }
            });
            
            // Nettoyer le pad à la fermeture de la modale
            modal.addEventListener('hidden.bs.modal', () => {
                signaturePad.clear();
            });
        }

        // Appliquer la fonction à la modale de création
        const createModal = document.getElementById('createProductionFollowUpModal');
        if (createModal) {
            setupSignaturePad(createModal);
        }

        // Appliquer la fonction à chaque modale d'édition
        document.querySelectorAll('[id^="editFollowUpModal"]').forEach(editModal => {
            setupSignaturePad(editModal);
        });
    });
</script>
@endpush
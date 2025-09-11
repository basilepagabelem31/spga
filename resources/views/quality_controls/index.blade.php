@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-0">📋 Liste des Contrôles Qualité</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createQualityControlModal">
            <i class="fas fa-plus-circle me-1"></i> Ajouter un contrôle
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-4 mb-4">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm rounded-4 mb-4">{{ session('error') }}</div>
    @endif

    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Date du contrôle</th>
                            <th>Produit</th>
                            <th>Référence du lot</th>
                            <th>Résultat</th>
                            <th>Signature</th> <th class="text-end pe-4">Actions</th>

                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($qualityControls as $qualityControl)
                            <tr>
                                <td>{{ $qualityControl->id }}</td>
                                <td>{{ $qualityControl->control_date->format('d/m/Y') }}</td>
                                <td>{{ $qualityControl->product->name }}</td>
                                <td>{{ $qualityControl->lot_reference ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $badgeClass = '';
                                        switch ($qualityControl->control_result) {
                                            case 'Conforme':
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'Non conforme':
                                                $badgeClass = 'bg-danger';
                                                break;
                                            case 'À réévaluer':
                                                $badgeClass = 'bg-warning text-dark';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $qualityControl->control_result }}</span>
                                </td>

                                 <td>
                @if ($qualityControl->responsible_signature_qc)
                    <img src="{{ $qualityControl->responsible_signature_qc }}" alt="Signature du responsable" style="height: 50px; width: auto; border: 1px solid #ddd; border-radius: 4px;">
                @else
                    <span class="text-muted">Pas de signature</span>
                @endif
            </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('quality_controls.show', $qualityControl) }}" class="btn btn-sm btn-outline-info" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editQualityControlModal{{ $qualityControl->id }}" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteQualityControlModal{{ $qualityControl->id }}" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            {{-- Modales pour chaque contrôle --}}
                            @include('quality_controls.partials.edit_modal', ['qualityControl' => $qualityControl, 'controllers' => $controllers, 'products' => $products])
                            @include('quality_controls.partials.delete_modal', ['qualityControl' => $qualityControl])
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucun contrôle qualité n'a été enregistré.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $qualityControls->links() }}
    </div>
</div>

{{-- Modale de création (incluse en dehors du tableau) --}}
@include('quality_controls.partials.create_modal', ['controllers' => $controllers, 'products' => $products])

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
 document.addEventListener('DOMContentLoaded', function () {
    // Fonction pour initialiser le pad de signature
    function setupSignaturePad(modal) {
        const modalId = modal.id;
        const qualityControlId = modalId.replace('editQualityControlModal', '');

        const canvas = modal.querySelector(`#signature-pad-edit-${qualityControlId}`);
        const clearButton = modal.querySelector(`#clear-signature-edit-${qualityControlId}`);
        const outputInput = modal.querySelector(`#signature-output-edit-${qualityControlId}`);
        
        if (!canvas || !clearButton || !outputInput) return;

        const signaturePad = new SignaturePad(canvas);

        // Gérer l'ouverture de la modale
        modal.addEventListener('shown.bs.modal', () => {
            const signatureDataElement = modal.querySelector('.signature-data');
            const existingSignatureData = signatureDataElement ? signatureDataElement.dataset.signature : null;

            signaturePad.clear();
            if (existingSignatureData) {
                // Pour que la signature s'affiche correctement, le canevas doit être visible
                // et avoir les bonnes dimensions. Cette ligne assure que le canevas se redimensionne.
                signaturePad.fromDataURL(existingSignatureData);
            }
        });

        // Gérer le clic sur le bouton "Effacer"
        clearButton.addEventListener('click', () => {
            signaturePad.clear();
            outputInput.value = '';
        });

        // Gérer la fermeture de la modale
        modal.addEventListener('hidden.bs.modal', () => {
            signaturePad.clear();
        });

        // Gérer la soumission du formulaire
        modal.querySelector('form').addEventListener('submit', function () {
            if (!signaturePad.isEmpty()) {
                outputInput.value = signaturePad.toDataURL();
            } else {
                const signatureDataElement = modal.querySelector('.signature-data');
                const existingSignatureData = signatureDataElement ? signatureDataElement.dataset.signature : null;
                outputInput.value = existingSignatureData || '';
            }
        });
    }

    // Initialisation de la modale de création
    const createModal = document.getElementById('createQualityControlModal');
    if (createModal) {
        const canvas = createModal.querySelector('#signature-pad-create');
        const clearButton = createModal.querySelector('#clear-signature-create');
        const outputInput = createModal.querySelector('#signature-output-create');

        const signaturePad = new SignaturePad(canvas);

        clearButton.addEventListener('click', () => {
            signaturePad.clear();
            outputInput.value = '';
        });
        
        createModal.querySelector('form').addEventListener('submit', function () {
            if (!signaturePad.isEmpty()) {
                outputInput.value = signaturePad.toDataURL();
            } else {
                outputInput.value = '';
            }
        });
    }


    // Initialisation de chaque modale d'édition
    document.querySelectorAll('[id^="editQualityControlModal"]').forEach(editModal => {
        setupSignaturePad(editModal);
    });
});

</script>
@endpush
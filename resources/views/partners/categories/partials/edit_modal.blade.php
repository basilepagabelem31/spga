<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editCategoryModalLabel{{ $category->id }}">Modifier la catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <form action="{{ route('partenaire.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_name_{{ $category->id }}" class="form-label">Nom de la catégorie</label>
                        <input type="text" class="form-control" id="edit_name_{{ $category->id }}" name="name" value="{{ $category->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description_{{ $category->id }}" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description_{{ $category->id }}" name="description" rows="3">{{ $category->description }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-outline-warning">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
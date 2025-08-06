<div class="modal fade" id="createRolePermissionModal" tabindex="-1" aria-labelledby="createRolePermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRolePermissionModalLabel">Attribuer une permission à un rôle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('role_has_permissions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role_id_create" class="form-label">Rôle <span class="text-danger">*</span></label>
                        <select class="form-select" id="role_id_create" name="role_id" required>
                            <option value="">Sélectionner un rôle</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="permission_id_create" class="form-label">Permission <span class="text-danger">*</span></label>
                        <select class="form-select" id="permission_id_create" name="permission_id" required>
                            <option value="">Sélectionner une permission</option>
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->id }}" {{ old('permission_id') == $permission->id ? 'selected' : '' }}>
                                    {{ $permission->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Attribuer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Inclure Select2 CSS et JS (si non déjà dans le layout principal) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- jQuery est un prérequis pour Select2 --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var createModal = document.getElementById('createRolePermissionModal');
        createModal.addEventListener('shown.bs.modal', function () {
            $('#role_id_create').select2({
                dropdownParent: $('#createRolePermissionModal')
            });
            $('#permission_id_create').select2({
                dropdownParent: $('#createRolePermissionModal')
            });
        });

        if ($(createModal).hasClass('show')) {
            $('#role_id_create').select2({
                dropdownParent: $('#createRolePermissionModal')
            });
            $('#permission_id_create').select2({
                dropdownParent: $('#createRolePermissionModal')
            });
        }
    });
</script>

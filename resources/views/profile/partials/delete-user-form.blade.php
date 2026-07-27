<div class="row">
    <div class="col-12">
        <p class="text-muted mb-4">
            <i class="fa-solid fa-exclamation-circle me-2 text-danger"></i>
            Une fois votre compte supprimé, toutes ses ressources et données seront supprimées définitivement. Avant de supprimer votre compte, veuillez télécharger toute donnée que vous souhaitez conserver.
        </p>

        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
            <i class="fa-solid fa-trash me-2"></i>
            Supprimer le compte
        </button>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirm-user-deletion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    Confirmer la suppression du compte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-body">
                    <p class="mb-3">
                        Êtes-vous certain de vouloir supprimer votre compte? 
                    </p>
                    <p class="text-muted small">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Une fois votre compte supprimé, toutes les ressources et données seront supprimées définitivement. 
                        Veuillez entrer votre mot de passe pour confirmer la suppression.
                    </p>

                    <div class="mb-3 mt-4">
                        <label for="password" class="form-label">
                            <i class="fa-solid fa-key me-2"></i>
                            Mot de passe
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                            placeholder="Entrez votre mot de passe..."
                            required
                        >
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times me-2"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash me-2"></i>
                        Oui, supprimer mon compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

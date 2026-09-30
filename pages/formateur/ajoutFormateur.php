<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../traitements/formateurs/ajout.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Ajouter une nouvelle formatrice</h2>
            <a href="index.php" class="btn btn-outline-secondary">← Retour à la liste</a>
        </div>
        <hr>

        <?php if (isset($errorMsg)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $errorMsg; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($successMsg)): ?>
            <div class="alert alert-success" role="alert">
                <?= $successMsg; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom de la formatrice *</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>

            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom de la formatrice *</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required>
            </div>

            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone *</label>
                <input type="text" class="form-control" id="telephone" name="telephone" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email de la formatrice *</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="specialite" class="form-label">Spécialité</label>
                <textarea class="form-control" id="specialite" name="specialite" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" name="validate">Enregistrer la formatrice</button>
        </form>
    </div>
</body>
</html>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../traitements/formations/modift.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Modifier la formation</h2>
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

        <?php if (isset($formationInfos)): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="titre" class="form-label">Nom / Titre de la formation *</label>
                    <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($titre); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($description ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="duree" class="form-label">Durée</label>
                    <input type="text" class="form-control" id="duree" name="duree" value="<?= htmlspecialchars($duree ?? ''); ?>" placeholder="Ex: 3 mois, 40 heures...">
                </div>

                <div class="mb-3">
                    <label for="prix" class="form-label">Prix (FCFA) *</label>
                    <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="<?= htmlspecialchars($prix); ?>" required>
                </div>

                <button type="submit" class="btn btn-warning" name="validate">Enregistrer les modifications</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
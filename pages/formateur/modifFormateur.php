<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../traitements/formateurs/modification.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Modifier une formatrice</h2>
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

        <?php if (isset($formateurInfos)): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($nom); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom *</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone *</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="date_naissance" class="form-label">Date de naissance *</label>
                    <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($date_naissance); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <textarea class="form-control" id="adresse" name="adresse" rows="3"><?= htmlspecialchars($adresse ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-warning" name="validate">Enregistrer les modifications</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
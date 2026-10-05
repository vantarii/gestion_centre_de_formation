<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../traitements/inscriptions/ajout.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Nouvelle Inscription</h2>
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
                <label for="id_etudiant" class="form-label">Sélectionner l'étudiant *</label>
                <select class="form-select" id="id_etudiant" name="id_etudiant" required>
                    <option value="" selected disabled>-- Choisir un étudiant --</option>
                    <?php foreach ($etudiants as $etudiant): ?>
                        <option value="<?= $etudiant['id']; ?>">
                            <?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_formation" class="form-label">Sélectionner la formation *</label>
                <select class="form-select" id="id_formation" name="id_formation" required>
                    <option value="" selected disabled>-- Choisir une formation --</option>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?= $formation['id']; ?>">
                            <?= htmlspecialchars($formation['titre']); ?> (<?= number_format($formation['prix'], 2, ',', ' '); ?> FCFA)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success" name="validate">Inscrire l'étudiant</button>
        </form>
    </div>
</body>
</html>
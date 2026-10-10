<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../traitements/inscriptions/modif.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5" style="max-width: 600px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modifier l'inscription #<?= htmlspecialchars($inscriptionInfos['id'] ?? ''); ?></h2>
            <a href="index.php" class="btn btn-secondary">← Retour</a>
        </div>

        <?php if (isset($errorMsg)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($inscriptionInfos)): ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        
                        <!-- Choix de l'étudiant -->
                        <div class="mb-3">
                            <label class="form-label">Étudiante :</label>
                            <select name="etudiant_id" class="form-select" required>
                                <option value="">-- Sélectionner une étudiante --</option>
                                <?php if (!empty($etudiants)): ?>
                                    <?php foreach ($etudiants as $etudiant): ?>
                                        <option value="<?= $etudiant['id']; ?>" <?= ($etudiant['id'] == $inscriptionInfos['etudiant_id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Choix de la formation -->
                        <div class="mb-3">
                            <label class="form-label">Formation :</label>
                            <select name="formation_id" class="form-select" required>
                                <option value="">-- Sélectionner une formation --</option>
                                <?php if (!empty($formations)): ?>
                                    <?php foreach ($formations as $formation): ?>
                                        <option value="<?= $formation['id']; ?>" <?= ($formation['id'] == $inscriptionInfos['formation_id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($formation['titre']) . ' (' . number_format($formation['prix'], 0, ',', ' ') . ' FCFA)'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="validate" class="btn btn-warning">
                                Enregistrer les modifications
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
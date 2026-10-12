<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../traitements/paiements/modif.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5" style="max-width: 650px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modifier le Paiement</h2>
            <a href="index.php" class="btn btn-secondary">← Retour</a>
        </div>

        <?php if (isset($errorMsg)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($paiement)): ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Inscription concernée :</label>
                            <select name="inscription_id" class="form-select" required>
                                <?php foreach ($inscriptions as $insc): ?>
                                    <option value="<?= $insc['inscription_id']; ?>" <?= ($insc['inscription_id'] == $paiement['inscription_id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($insc['etudiant_nom'] . ' ' . $insc['etudiant_prenom'] . ' — ' . $insc['formation_titre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Montant (FCFA) :</label>
                            <input type="number" step="0.01" min="1" name="montant" class="form-control" value="<?= htmlspecialchars($paiement['montant']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mode de paiement :</label>
                            <select name="mode_paiement" class="form-select" required>
                                <?php 
                                $modes = ['Espèces', 'TMoney', 'Flooz', 'Carte bancaire', 'Virement'];
                                foreach ($modes as $m): 
                                ?>
                                    <option value="<?= $m; ?>" <?= ($m === $paiement['mode_paiement']) ? 'selected' : ''; ?>>
                                        <?= $m; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Référence :</label>
                            <input type="text" name="reference" class="form-control" value="<?= htmlspecialchars($paiement['reference'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date du paiement :</label>
                            <input type="datetime-local" name="date_paiement" class="form-control" value="<?= !empty($paiement['date_paiement']) ? date('Y-m-d\TH:i', strtotime($paiement['date_paiement'])) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Statut du paiement :</label>
                            <select name="statut" class="form-select" required>
                                <option value="1" <?= (intval($paiement['statut']) === 1) ? 'selected' : ''; ?>>Validé</option>
                                <option value="0" <?= (intval($paiement['statut']) === 0) ? 'selected' : ''; ?>>En attente</option>
                                <option value="2" <?= (intval($paiement['statut']) === 2) ? 'selected' : ''; ?>>Échoué</option>
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
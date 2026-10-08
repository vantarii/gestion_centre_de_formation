<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Récupération des inscriptions actives pour la sélection
$inscriptions = $bdd->query('
    SELECT 
        i.id AS inscription_id,
        e.nom AS etudiant_nom,
        e.prenom AS etudiant_prenom,
        f.titre AS formation_titre,
        f.prix AS formation_prix
    FROM inscription i
    JOIN etudiant e ON i.etudiant_id = e.id
    JOIN formation f ON i.formation_id = f.id
    ORDER BY e.nom ASC
')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5" style="max-width: 650px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Nouveau Paiement</h2>
            <a href="index.php" class="btn btn-secondary">← Retour</a>
        </div>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error_msg']); ?>
                <?php unset($_SESSION['error_msg']); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="../../traitements/paiements/ajout.php" method="POST">
                    
                    <!-- Sélection de l'inscription -->
                    <div class="mb-3">
                        <label class="form-label">Inscription concernée * :</label>
                        <select name="inscription_id" class="form-select" required>
                            <option value="">-- Sélectionner l'étudiant et la formation --</option>
                            <?php foreach ($inscriptions as $insc): ?>
                                <option value="<?= $insc['inscription_id']; ?>">
                                    <?= htmlspecialchars($insc['etudiant_nom'] . ' ' . $insc['etudiant_prenom'] . ' — ' . $insc['formation_titre'] . ' (' . number_format($insc['formation_prix'], 0, ',', ' ') . ' FCFA)'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Montant -->
                    <div class="mb-3">
                        <label class="form-label">Montant du règlement (FCFA) * :</label>
                        <input type="number" step="0.01" min="1" name="montant" class="form-control" placeholder="Ex: 50000" required>
                    </div>

                    <!-- Mode de paiement -->
                    <div class="mb-3">
                        <label class="form-label">Mode de paiement * :</label>
                        <select name="mode_paiement" class="form-select" required>
                            <option value="Espèces">Espèces</option>
                            <option value="TMoney">TMoney</option>
                            <option value="Flooz">Flooz</option>
                            <option value="Carte bancaire">Carte bancaire</option>
                            <option value="Virement">Virement</option>
                        </select>
                    </div>

                    <!-- Référence du paiement -->
                    <div class="mb-3">
                        <label class="form-label">Référence de la transaction * :</label>
                        <input type="text" name="reference" class="form-control" placeholder="Ex: PAY-20261008-001 ou Réf TMoney/Flooz" required>
                    </div>

                    <!-- Date du paiement -->
                    <div class="mb-3">
                        <label class="form-label">Date du paiement * :</label>
                        <input type="datetime-local" name="date_paiement" class="form-control" value="<?= date('Y-m-d\TH:i'); ?>" required>
                    </div>

                    <!-- Statut du paiement -->
                    <div class="mb-3">
                        <label class="form-label">Statut du paiement * :</label>
                        <select name="statut" class="form-select" required>
                            <option value="Validé" selected>Validé</option>
                            <option value="En attente">En attente</option>
                            <option value="Échoué">Échoué</option>
                        </select>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="validate" class="btn btn-success">
                            Enregistrer le paiement
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$idPaiement = intval($_GET['id']);

$getPaiement = $bdd->prepare('SELECT * FROM paiement WHERE id = ?');
$getPaiement->execute([$idPaiement]);
$paiement = $getPaiement->fetch(PDO::FETCH_ASSOC);

if (!$paiement) {
    $_SESSION['error_msg'] = "Paiement introuvable.";
    header('Location: index.php');
    exit();
}

$inscriptions = $bdd->query('
    SELECT 
        i.id AS inscription_id,
        e.nom AS etudiant_nom,
        e.prenom AS etudiant_prenom,
        f.titre AS formation_titre
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
            <h2>Modifier le Paiement #<?= htmlspecialchars($paiement['id']); ?></h2>
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
                <form action="../../traitements/paiements/modif.php" method="POST">
                    
                    <input type="hidden" name="id_paiement" value="<?= $paiement['id']; ?>">

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
                        <input type="datetime-local" name="date_paiement" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($paiement['date_paiement'])); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Statut du paiement :</label>
                        <select name="statut" class="form-select" required>
                            <?php 
                            $statuts = ['Validé', 'En attente', 'Échoué'];
                            foreach ($statuts as $st): 
                            ?>
                                <option value="<?= $st; ?>" <?= ($st === $paiement['statut']) ? 'selected' : ''; ?>>
                                    <?= $st; ?>
                                </option>
                            <?php endforeach; ?>
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
    </div>
</body>
</html>
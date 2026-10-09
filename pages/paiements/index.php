<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Filtres de recherche
$search = !empty($_GET['search']) ? trim(htmlspecialchars($_GET['search'])) : null;
$modeFilter = !empty($_GET['mode_paiement']) ? trim(htmlspecialchars($_GET['mode_paiement'])) : null;

try {
    $sql = '
        SELECT 
            p.id,
            p.reference,
            p.montant,
            p.mode_paiement,
            p.statut,
            p.date_paiement,
            e.nom AS etudiant_nom,
            e.prenom AS etudiant_prenom,
            f.titre AS formation_titre
        FROM paiement p
        JOIN inscription i ON p.inscription_id = i.id
        JOIN etudiant e ON i.etudiant_id = e.id
        JOIN formation f ON i.formation_id = f.id
        WHERE 1=1
    ';

    $params = [];

    if ($search) {
        $sql .= ' AND (p.reference LIKE ? OR e.nom LIKE ? OR e.prenom LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($modeFilter) {
        $sql .= ' AND p.mode_paiement = ?';
        $params[] = $modeFilter;
    }

    $sql .= ' ORDER BY p.id DESC';

    $getPaiements = $bdd->prepare($sql);
    $getPaiements->execute($params);
    $paiements = $getPaiements->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errorMsg = "Erreur lors de la récupération des paiements : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des Paiements</h2>
            <a href="ajoutPaiement.php" class="btn btn-success">
                + Nouveau paiement
            </a>
        </div>

        <!-- Notifications -->
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success_msg']); ?>
                <?php unset($_SESSION['success_msg']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error_msg']); ?>
                <?php unset($_SESSION['error_msg']); ?>
            </div>
        <?php endif; ?>

        <!-- Barre de recherche -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher par référence, nom ou prénom..." value="<?= htmlspecialchars($search ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="mode_paiement" class="form-select">
                            <option value="">-- Tous les modes de paiement --</option>
                            <?php 
                            $modesList = ['Espèces', 'TMoney', 'Flooz', 'Carte bancaire', 'Virement'];
                            foreach ($modesList as $mode):
                            ?>
                                <option value="<?= $mode; ?>" <?= ($modeFilter === $mode) ? 'selected' : ''; ?>>
                                    <?= $mode; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                        <a href="index.php" class="btn btn-outline-secondary">Effacer</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des Paiements -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Référence</th>
                            <th>Étudiant</th>
                            <th>Formation</th>
                            <th>Montant (FCFA)</th>
                            <th>Mode</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($paiements)): ?>
                            <?php foreach ($paiements as $paiement): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($paiement['reference'] ?? 'N/A'); ?></code></td>
                                    <td>
                                        <strong><?= htmlspecialchars($paiement['etudiant_nom'] . ' ' . $paiement['etudiant_prenom']); ?></strong>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($paiement['formation_titre']); ?></span></td>
                                    <td><strong class="text-success"><?= number_format($paiement['montant'], 2, ',', ' '); ?></strong></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($paiement['mode_paiement']); ?></span></td>
                                    <td>
                                        <?php 
                                        $st = intval($paiement['statut'] ?? 1);
                                        if ($st === 1) {
                                            echo '<span class="badge bg-success">Validé</span>';
                                        } elseif ($st === 0) {
                                            echo '<span class="badge bg-warning text-dark">En attente</span>';
                                        } else {
                                            echo '<span class="badge bg-danger">Échoué</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= !empty($paiement['date_paiement']) ? date('d/m/Y H:i', strtotime($paiement['date_paiement'])) : '-'; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="modifPaiement.php?uuid=<?= $p['paiement_uuid'] ?? $p['id']; ?>" class="btn btn-warning">Modifier</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Aucun paiement trouvé.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
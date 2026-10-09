<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verification de la session et connexion BDD
require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Récupération de la liste des formations actives (status = 1)
try {
    $getFormations = $bdd->prepare('SELECT * FROM formation WHERE status = 1 ORDER BY id DESC');
    $getFormations->execute();
    $formations = $getFormations->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMsg = "Erreur lors de la récupération des formations : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Liste des Formations</h2>
            <a href="ajoutFormation.php" class="btn btn-success">
                + Ajouter une formation
            </a>
        </div>

        <?php if (isset($errorMsg)): ?>
            <div class="alert alert-danger"><?= $errorMsg; ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Durée</th>
                            <th>Prix (FCFA)</th>
                            <th>Date de création</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($formations)): ?>
                            <?php foreach ($formations as $formation): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($formation['titre']); ?></strong></td>
                                    <td><?= htmlspecialchars($formation['duree'] ?? 'N/A'); ?></td>
                                    <td><?= number_format($formation['prix'], 2, ',', ' '); ?></td>
                                    <td>
                                        <?= !empty($formation['date_created']) ? date('d/m/Y H:i', strtotime($formation['date_created'])) : '-'; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="modifFormation.php?id=<?= $formation['id']; ?>" class="btn btn-sm btn-warning me-1">Modifier</a>
                                        <a href="supprFormation.php?id=<?= $formation['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cette formation ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Aucune formation enregistrée pour le moment.
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
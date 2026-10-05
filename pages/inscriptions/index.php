<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Récupération des inscriptions actives (status = 1) avec les informations des étudiants et des formations
try {
    $getInscriptions = $bdd->prepare('
        SELECT 
            i.id,
            i.date_inscription,
            i.date_created,
            e.nom AS etudiant_nom,
            e.prenom AS etudiant_prenom,
            f.titre AS formation_titre,
            f.prix AS formation_prix
        FROM inscription i
        JOIN etudiant e ON i.id_etudiant = e.id
        JOIN formation f ON i.id_formation = f.id
        WHERE i.status = 1
        ORDER BY i.id DESC
    ');
    $getInscriptions->execute();
    $inscriptions = $getInscriptions->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMsg = "Erreur lors de la récupération des inscriptions : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestion des Inscriptions</h2>
            <a href="ajoutInscription.php" class="btn btn-success">
                + Nouvelle inscription
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
                            <th>#ID</th>
                            <th>Étudiant</th>
                            <th>Formation</th>
                            <th>Prix (FCFA)</th>
                            <th>Date d'inscription</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inscriptions)): ?>
                            <?php foreach ($inscriptions as $inscription): ?>
                                <tr>
                                    <td><?= htmlspecialchars($inscription['id']); ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($inscription['etudiant_nom'] . ' ' . $inscription['etudiant_prenom']); ?></strong>
                                    </td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($inscription['formation_titre']); ?></span></td>
                                    <td><?= number_format($inscription['formation_prix'], 2, ',', ' '); ?></td>
                                    <td>
                                        <?= !empty($inscription['date_inscription']) ? date('d/m/Y H:i', strtotime($inscription['date_inscription'])) : date('d/m/Y H:i', strtotime($inscription['date_created'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="supprInscription.php?id=<?= $inscription['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment annuler cette inscription ?');">
                                            Annuler / Supprimer
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Aucune inscription enregistrée pour le moment.
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
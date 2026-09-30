<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verification de la session et connexion BDD avec __DIR__
require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Récupération de la liste des formateurs (table: formateur)
try {
    $getFormateurs = $bdd->prepare('SELECT * FROM formateur ORDER BY id DESC');
    $getFormateurs->execute();
    $formateurs = $getFormateurs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMsg = "Erreur lors de la récupération des formatrices : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Liste des Formatrices</h2>
            <a href="ajoutFormateur.php" class="btn btn-success">
                + Ajouter une formatrice
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
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Spécialité</th>
                            <th>Date d'ajout</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($formateurs)): ?>
                            <?php foreach ($formateurs as $formateur): ?>
                                <tr>
                                    <td><?= htmlspecialchars($formateur['id']); ?></td>
                                    <td><strong><?= htmlspecialchars($formateur['nom']); ?></strong></td>
                                    <td><?= htmlspecialchars($formateur['prenom']); ?></td>
                                    <td><?= htmlspecialchars($formateur['email']); ?></td>
                                    <td><?= htmlspecialchars($formateur['telephone']); ?></td>
                                    <td><?= htmlspecialchars($formateur['specialite'] ?? '-'); ?></td>
                                    <td>
                                        <?= !empty($formateur['date_created']) ? date('d/m/Y H:i', strtotime($formateur['date_created'])) : '-'; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="modifFormateur.php?id=<?= $formateur['id']; ?>" class="btn btn-sm btn-warning me-1">Modifier</a>
                                        <a href="supprFormateur.php?id=<?= $formateur['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cette formatrice ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Aucune formatrice enregistrée pour le moment.
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
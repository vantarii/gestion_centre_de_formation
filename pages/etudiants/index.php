<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verification de la session et connexion BDD avec __DIR__
require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Récupération de la liste des étudiants
// Récupérer UNIQUEMENT les étudiants actifs (status = 1)
try {
    $getEtudiants = $bdd->prepare('SELECT * FROM etudiant WHERE status = 1 ORDER BY id DESC');
    $getEtudiants->execute();
    $etudiants = $getEtudiants->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMsg = "Erreur lors de la récupération des étudiants : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Liste des Étudiantes</h2>
            <a href="ajoutEtudiant.php" class="btn btn-success">
                + Ajouter une étudiante
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
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Date d'ajout</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($etudiants)): ?>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($etudiant['nom']); ?></strong></td>
                                    <td><?= htmlspecialchars($etudiant['prenom']); ?></td>
                                    <td><?= htmlspecialchars($etudiant['email']); ?></td>
                                    <td><?= htmlspecialchars($etudiant['telephone']); ?></td>
                                    <td>
                                        <?= !empty($etudiant['date_created']) ? date('d/m/Y H:i', strtotime($etudiant['date_created'])) : '-'; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="modifEtudiant.php?uuid=<?= !empty($etudiant['etudiant_uuid']) ? $etudiant['etudiant_uuid'] : $etudiant['id']; ?>" class="btn btn-sm btn-warning me-1">Modifier</a>
                                        <a href="supprEtudiant.php?id=<?= $etudiant['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet étudiant ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Aucune étudiante enregistrée pour le moment.
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
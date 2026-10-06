<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// Récupération des étudiants et formations
$etudiants = $bdd->query('SELECT id, nom, prenom FROM etudiant WHERE status = 1 ORDER BY nom ASC')->fetchAll(PDO::FETCH_ASSOC);
$formations = $bdd->query('SELECT id, titre, prix FROM formation WHERE status = 1 ORDER BY titre ASC')->fetchAll(PDO::FETCH_ASSOC);

// Traitement de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    $etudiantId = !empty($_POST['etudiant_id']) ? intval($_POST['etudiant_id']) : null;
    $formationId = !empty($_POST['formation_id']) ? intval($_POST['formation_id']) : null;

    if ($etudiantId && $formationId) {
        // Vérification des doublons
        $check = $bdd->prepare('SELECT id FROM inscription WHERE etudiant_id = ? AND formation_id = ? AND status = 1');
        $check->execute([$etudiantId, $formationId]);

        if ($check->rowCount() > 0) {
            $errorMsg = "Cet étudiant est déjà inscrit à cette formation !";
        } else {
            try {
                $insert = $bdd->prepare('INSERT INTO inscription (etudiant_id, formation_id, date_inscription, status) VALUES (?, ?, NOW(), 1)');
                $insert->execute([$etudiantId, $formationId]);

                $_SESSION['success_msg'] = "L'inscription a été enregistrée avec succès !";
                header('Location: index.php');
                exit();
            } catch (PDOException $e) {
                $errorMsg = "Erreur lors de l'enregistrement : " . $e->getMessage();
            }
        }
    } else {
        $errorMsg = "Veuillez sélectionner un étudiant et une formation.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="container mt-5" style="max-width: 600px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Nouvelle inscription</h2>
            <a href="index.php" class="btn btn-secondary">← Retour</a>
        </div>

        <?php if (isset($errorMsg)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label">Étudiant :</label>
                        <select name="etudiant_id" class="form-select" required>
                            <option value="">-- Choisir un étudiant --</option>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <option value="<?= $etudiant['id']; ?>">
                                    <?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Formation :</label>
                        <select name="formation_id" class="form-select" required>
                            <option value="">-- Choisir une formation --</option>
                            <?php foreach ($formations as $formation): ?>
                                <option value="<?= $formation['id']; ?>">
                                    <?= htmlspecialchars($formation['titre']) . ' (' . number_format($formation['prix'], 0, ',', ' ') . ' FCFA)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="validate" class="btn btn-success">
                            Valider l'inscription
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>
</html>
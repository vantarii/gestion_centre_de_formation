<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

// 1. Vérification et récupération de l'ID dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$idInscription = intval($_GET['id']);

// 2. Récupération des informations de l'inscription actuelle
$getInscription = $bdd->prepare('SELECT * FROM inscription WHERE id = ? AND status = 1');
$getInscription->execute([$idInscription]);
$inscription = $getInscription->fetch(PDO::FETCH_ASSOC);

if (!$inscription) {
    $_SESSION['error_msg'] = "Inscription introuvable ou déjà annulée.";
    header('Location: index.php');
    exit();
}

// 3. Récupération des étudiants et formations actifs
$etudiants = $bdd->query('SELECT id, nom, prenom FROM etudiant WHERE status = 1 ORDER BY nom ASC')->fetchAll(PDO::FETCH_ASSOC);
$formations = $bdd->query('SELECT id, titre, prix FROM formation WHERE status = 1 ORDER BY titre ASC')->fetchAll(PDO::FETCH_ASSOC);

// 4. Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    $etudiantId = !empty($_POST['etudiant_id']) ? intval($_POST['etudiant_id']) : null;
    $formationId = !empty($_POST['formation_id']) ? intval($_POST['formation_id']) : null;

    if ($etudiantId && $formationId) {
        // Vérifier s'il n'existe pas DÉJÀ une AUTRE inscription active identique
        $checkDuplicate = $bdd->prepare('
            SELECT id FROM inscription 
            WHERE etudiant_id = ? AND formation_id = ? AND status = 1 AND id != ?
        ');
        $checkDuplicate->execute([$etudiantId, $formationId, $idInscription]);

        if ($checkDuplicate->rowCount() > 0) {
            $errorMsg = "Cette étudiante est déjà inscrite à cette formation !";
        } else {
            try {
                $update = $bdd->prepare('
                    UPDATE inscription 
                    SET etudiant_id = ?, formation_id = ? 
                    WHERE id = ?
                ');
                $update->execute([$etudiantId, $formationId, $idInscription]);

                $_SESSION['success_msg'] = "L'inscription a été modifiée avec succès.";
                header('Location: index.php');
                exit();
            } catch (PDOException $e) {
                $errorMsg = "Erreur lors de la modification : " . $e->getMessage();
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
            <h2>Modifier l'inscription #<?= htmlspecialchars($inscription['id']); ?></h2>
            <a href="index.php" class="btn btn-secondary">← Retour</a>
        </div>

        <?php if (isset($errorMsg)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    
                    <!-- Choix de l'étudiant -->
                    <div class="mb-3">
                        <label class="form-label">Étudiant :</label>
                        <select name="etudiant_id" class="form-select" required>
                            <option value="">-- Sélectionner un étudiant --</option>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <option value="<?= $etudiant['id']; ?>" <?= ($etudiant['id'] == $inscription['etudiant_id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Choix de la formation -->
                    <div class="mb-3">
                        <label class="form-label">Formation :</label>
                        <select name="formation_id" class="form-select" required>
                            <option value="">-- Sélectionner une formation --</option>
                            <?php foreach ($formations as $formation): ?>
                                <option value="<?= $formation['id']; ?>" <?= ($formation['id'] == $inscription['formation_id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($formation['titre']) . ' (' . number_format($formation['prix'], 0, ',', ' ') . ' FCFA)'; ?>
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
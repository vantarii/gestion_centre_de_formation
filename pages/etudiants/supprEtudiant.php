<?php
require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idEtudiant = $_GET['id'];

    // 1. Vérifier si l'étudiant existe et est actif (status = 1)
    $checkIfEtudiantExists = $bdd->prepare('SELECT id FROM etudiant WHERE id = ? AND status = 1');
    $checkIfEtudiantExists->execute([$idEtudiant]);

    if ($checkIfEtudiantExists->rowCount() > 0) {

        // 2. Suppression logique : passage de status à 0
        $softDeleteEtudiant = $bdd->prepare('
            UPDATE etudiant 
            SET status = 0, date_updated = NOW() 
            WHERE id = ?
        ');
        $softDeleteEtudiant->execute([$idEtudiant]);

        // 3. Redirection vers la liste des étudiants
        header('Location: index.php');
        exit();

    } else {
        $errorMsg = "Aucune étudiante trouvée avec cet ID.";
    }
} else {
    $errorMsg = "Aucun identifiant n'a été fourni.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <div class="container mt-5">
        <div class="alert alert-danger"><?= $errorMsg; ?></div>
        <a href="index.php" class="btn btn-secondary">← Retour à la liste</a>
    </div>
</body>
</html>
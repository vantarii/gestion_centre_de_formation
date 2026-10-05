<?php
require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idFormation = $_GET['id'];

    // 1. Vérifier si la formation existe et est active
    $checkIfFormationExists = $bdd->prepare('SELECT id FROM formation WHERE id = ? AND status = 1');
    $checkIfFormationExists->execute([$idFormation]);

    if ($checkIfFormationExists->rowCount() > 0) {

        // 2. Soft Delete : passer status à 0
        $softDeleteFormation = $bdd->prepare('
            UPDATE formation 
            SET status = 0, date_updated = NOW() 
            WHERE id = ?
        ');
        $softDeleteFormation->execute([$idFormation]);

        // 3. Redirection
        header('Location: index.php');
        exit();

    } else {
        $errorMsg = "Aucune formation active trouvée avec cet ID.";
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
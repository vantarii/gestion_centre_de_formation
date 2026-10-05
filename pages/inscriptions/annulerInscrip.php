<?php
require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idInscription = $_GET['id'];

    // 1. Vérifier si l'inscription existe et est active
    $checkIfInscriptionExists = $bdd->prepare('SELECT id FROM inscription WHERE id = ? AND status = 1');
    $checkIfInscriptionExists->execute([$idInscription]);

    if ($checkIfInscriptionExists->rowCount() > 0) {

        // 2. Soft Delete : passage de status à 0
        $softDeleteInscription = $bdd->prepare('
            UPDATE inscription 
            SET status = 0, date_updated = NOW() 
            WHERE id = ?
        ');
        $softDeleteInscription->execute([$idInscription]);

        // 3. Redirection
        header('Location: index.php');
        exit();

    } else {
        $errorMsg = "Aucune inscription active trouvée avec cet ID.";
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
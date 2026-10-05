<?php
require_once __DIR__ . '/../../traitements/users/security.php';
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idFormateur = $_GET['id'];

    // 1. Vérifier si la formatrice existe et n'est pas déjà supprimée (status != 0 ou NULL)
    $checkIfFormateurExists = $bdd->prepare('SELECT id FROM formateur WHERE id = ? AND (status = 1 OR status IS NULL)');
    $checkIfFormateurExists->execute([$idFormateur]);

    if ($checkIfFormateurExists->rowCount() > 0) {

        // 2. Suppression logique : passage de status à 0
        $softDeleteFormateur = $bdd->prepare('
            UPDATE formateur 
            SET status = 0, date_updated = NOW() 
            WHERE id = ?
        ');
        $softDeleteFormateur->execute([$idFormateur]);

        // 3. Redirection vers la liste des formatrices
        header('Location: index.php');
        exit();

    } else {
        $errorMsg = "Aucune formatrice active trouvée avec cet ID.";
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
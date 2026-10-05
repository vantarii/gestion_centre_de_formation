<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Récupération des informations de la formation à modifier
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idFormation = $_GET['id'];

    $getFormation = $bdd->prepare('SELECT * FROM formation WHERE id = ? AND status = 1');
    $getFormation->execute([$idFormation]);

    if ($getFormation->rowCount() > 0) {
        $formationInfos = $getFormation->fetch(PDO::FETCH_ASSOC);

        $titre = $formationInfos['titre'];
        $description = $formationInfos['description'];
        $duree = $formationInfos['duree'];
        $prix = $formationInfos['prix'];
    } else {
        $errorMsg = "Aucune formation active trouvée avec cet identifiant.";
    }
} else {
    $errorMsg = "Aucun identifiant transmis.";
}

// 2. Traitement de la mise à jour lors de l'envoi du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $newTitre = !empty($_POST['titre']) ? trim(htmlspecialchars($_POST['titre'])) : null;
    $newDescription = !empty($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : null;
    $newDuree = !empty($_POST['duree']) ? trim(htmlspecialchars($_POST['duree'])) : null;
    $newPrix = isset($_POST['prix']) && $_POST['prix'] !== '' ? floatval($_POST['prix']) : null;

    if ($newTitre && $newPrix !== null) {

        $updated_by = $_SESSION['id'] ?? 1;

        try {
            $updateFormation = $bdd->prepare('
                UPDATE formation 
                SET titre = ?, description = ?, duree = ?, prix = ?, date_updated = NOW(), updated_by = ?
                WHERE id = ? AND status = 1
            ');
            $updateFormation->execute([
                $newTitre,
                $newDescription,
                $newDuree,
                $newPrix,
                $updated_by,
                $idFormation
            ]);

            // Mise à jour des valeurs pour l'affichage du formulaire
            $titre = $newTitre;
            $description = $newDescription;
            $duree = $newDuree;
            $prix = $newPrix;

            $successMsg = "La formation a bien été mise à jour !";
        } catch (PDOException $e) {
            $errorMsg = "Erreur lors de la modification : " . $e->getMessage();
        }

    } else {
        $errorMsg = "Veuillez remplir au moins le titre et le prix de la formation.";
    }
}
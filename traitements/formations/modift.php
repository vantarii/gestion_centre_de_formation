<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Génère une chaîne aléatoire sécurisée pour formation_uuid
 */
function generateRandomString($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

// 1. Récupération de l'identifiant (uuid prioritaire, id numérique en repli)
$paramIdentifiant = $_GET['uuid'] ?? $_GET['id'] ?? $_POST['formation_uuid'] ?? $_POST['id_formation'] ?? null;

if (!empty($paramIdentifiant)) {

    // Recherche de la formation active par formation_uuid OU par id
    $getFormation = $bdd->prepare('SELECT * FROM formation WHERE (formation_uuid = ? OR id = ?) AND status = 1');
    $getFormation->execute([$paramIdentifiant, $paramIdentifiant]);

    if ($getFormation->rowCount() > 0) {
        $formation = $getFormation->fetch(PDO::FETCH_ASSOC);
        $idFormation = $formation['id'];

        // Si la formation n'a pas encore d'UUID enregistré, on lui en génère un automatiquement
        if (empty($formation['formation_uuid'])) {
            $newUuid = generateRandomString(6);
            $updateUuid = $bdd->prepare('UPDATE formation SET formation_uuid = ? WHERE id = ?');
            $updateUuid->execute([$newUuid, $idFormation]);
            $formation['formation_uuid'] = $newUuid;
        }

        $formationUuid = $formation['formation_uuid'];
        $titre = $formation['titre'];
        $description = $formation['description'] ?? '';
        $prix = $formation['prix'];
        $duree = $formation['duree'] ?? '';

    } else {
        $_SESSION['error_msg'] = "La formation demandée n'existe pas ou est inactive.";
    }
} else {
    $_SESSION['error_msg'] = "Aucun identifiant transmis.";
}

// 2. Traitement de la modification lors de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $newTitre = !empty($_POST['titre']) ? trim(htmlspecialchars($_POST['titre'])) : null;
    $newDescription = !empty($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : null;
    $newPrix = !empty($_POST['prix']) ? floatval($_POST['prix']) : null;
    $newDuree = !empty($_POST['duree']) ? trim(htmlspecialchars($_POST['duree'])) : null;

    if (isset($idFormation) && $newTitre && $newPrix && $newPrix > 0) {
        try {
            $updateFormation = $bdd->prepare('
                UPDATE formation 
                SET titre = ?, description = ?, prix = ?, duree = ?
                WHERE id = ? AND status = 1
            ');
            $updateFormation->execute([
                $newTitre,
                $newDescription,
                $newPrix,
                $newDuree,
                $idFormation
            ]);

            $_SESSION['success_msg'] = "La formation a été modifiée avec succès !";
            header('Location: ../../pages/formations/index.php');
            exit();

        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erreur lors de la modification : " . $e->getMessage();
            header('Location: ../../pages/formations/modifFormation.php?uuid=' . $formationUuid);
            exit();
        }
    } else {
        $_SESSION['error_msg'] = "Veuillez remplir correctement tous les champs obligatoires (Titre et Prix).";
        header('Location: ../../pages/formations/modifFormation.php?uuid=' . ($formationUuid ?? ''));
        exit();
    }
}
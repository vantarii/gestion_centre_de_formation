<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Génère une chaîne aléatoire sécurisée pour formateur_uuid
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
$paramIdentifiant = $_GET['uuid'] ?? $_GET['id'] ?? null;

if (!empty($paramIdentifiant)) {

    // Recherche du formateur actif par formateur_uuid OU par id
    $getFormateur = $bdd->prepare('SELECT * FROM formateur WHERE (formateur_uuid = ? OR id = ?) AND status = 1');
    $getFormateur->execute([$paramIdentifiant, $paramIdentifiant]);

    if ($getFormateur->rowCount() > 0) {
        $formateurInfos = $getFormateur->fetch(PDO::FETCH_ASSOC);

        $idFormateur = $formateurInfos['id'];

        // Si le formateur n'a pas encore d'UUID enregistré, on lui en génère un automatiquement
        if (empty($formateurInfos['formateur_uuid'])) {
            $newUuid = generateRandomString(6);
            $updateUuid = $bdd->prepare('UPDATE formateur SET formateur_uuid = ? WHERE id = ?');
            $updateUuid->execute([$newUuid, $idFormateur]);
            $formateurInfos['formateur_uuid'] = $newUuid;
        }

        $formateurUuid = $formateurInfos['formateur_uuid'];
        $nom = $formateurInfos['nom'];
        $prenom = $formateurInfos['prenom'];
        $email = $formateurInfos['email'];
        $telephone = $formateurInfos['telephone'];
        $specialite = $formateurInfos['specialite'] ?? '';

    } else {
        $errorMsg = "Aucun formateur actif trouvé avec cet identifiant.";
    }
} else {
    $errorMsg = "Aucun identifiant transmis.";
}

// 2. Traitement de la modification lors de l'envoi du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {

    $newNom = !empty($_POST['nom']) ? trim(htmlspecialchars($_POST['nom'])) : null;
    $newPrenom = !empty($_POST['prenom']) ? trim(htmlspecialchars($_POST['prenom'])) : null;
    $newEmail = !empty($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : null;
    $newTelephone = !empty($_POST['telephone']) ? trim(htmlspecialchars($_POST['telephone'])) : null;
    $newSpecialite = !empty($_POST['specialite']) ? trim(htmlspecialchars($_POST['specialite'])) : null;

    if ($newNom && $newPrenom && $newEmail) {

        try {
            $updateFormateur = $bdd->prepare('
                UPDATE formateur 
                SET nom = ?, prenom = ?, email = ?, telephone = ?, specialite = ?
                WHERE id = ? AND status = 1
            ');

            $updateFormateur->execute([
                $newNom,
                $newPrenom,
                $newEmail,
                $newTelephone,
                $newSpecialite,
                $idFormateur
            ]);

            // Mettre à jour les variables d'affichage du formulaire
            $nom = $newNom;
            $prenom = $newPrenom;
            $email = $newEmail;
            $telephone = $newTelephone;
            $specialite = $newSpecialite;

            $successMsg = "Les informations de la formatrice/du formateur ont été mises à jour avec succès !";

        } catch (PDOException $e) {
            $errorMsg = "Erreur lors de la mise à jour : " . $e->getMessage();
        }

    } else {
        $errorMsg = "Veuillez remplir tous les champs obligatoires (*).";
    }
}
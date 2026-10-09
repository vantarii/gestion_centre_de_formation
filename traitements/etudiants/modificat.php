<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Génère une chaîne aléatoire sécurisée pour etudiant_uuid
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

    // Recherche de l'étudiant actif par etudiant_uuid OU par id
    $getEtudiant = $bdd->prepare('SELECT * FROM etudiant WHERE (etudiant_uuid = ? OR id = ?) AND status = 1');
    $getEtudiant->execute([$paramIdentifiant, $paramIdentifiant]);

    if ($getEtudiant->rowCount() > 0) {
        $etudiantInfos = $getEtudiant->fetch(PDO::FETCH_ASSOC);

        $idEtudiant = $etudiantInfos['id'];

        // Si l'étudiant n'a pas encore d'UUID enregistré, on lui en génère un automatiquement
        if (empty($etudiantInfos['etudiant_uuid'])) {
            $newUuid = generateRandomString(6);
            $updateUuid = $bdd->prepare('UPDATE etudiant SET etudiant_uuid = ? WHERE id = ?');
            $updateUuid->execute([$newUuid, $idEtudiant]);
            $etudiantInfos['etudiant_uuid'] = $newUuid;
        }

        $etudiantUuid = $etudiantInfos['etudiant_uuid'];
        $nom = $etudiantInfos['nom'];
        $prenom = $etudiantInfos['prenom'];
        $email = $etudiantInfos['email'];
        $telephone = $etudiantInfos['telephone'];
        $adresse = $etudiantInfos['adresse'];

    } else {
        $errorMsg = "Aucun étudiant actif trouvé avec cet identifiant.";
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
    $newAdresse = !empty($_POST['adresse']) ? trim(htmlspecialchars($_POST['adresse'])) : null;

    if ($newNom && $newPrenom && $newEmail && $newTelephone) {

        $updated_by = $_SESSION['id'] ?? 1;

        try {
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $updateEtudiant = $bdd->prepare('
                UPDATE etudiant 
                SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ?, date_updated = NOW(), updated_by = ?
                WHERE id = ? AND status = 1
            ');

            $updateEtudiant->execute([
                $newNom,
                $newPrenom,
                $newEmail,
                $newTelephone,
                $newAdresse,
                $updated_by,
                $idEtudiant
            ]);

            // Mettre à jour les variables d'affichage du formulaire
            $nom = $newNom;
            $prenom = $newPrenom;
            $email = $newEmail;
            $telephone = $newTelephone;
            $adresse = $newAdresse;

            $successMsg = "Les informations de l'étudiant ont été mises à jour avec succès !";

        } catch (PDOException $e) {
            $errorMsg = "Erreur lors de la mise à jour : " . $e->getMessage();
        }

    } else {
        $errorMsg = "Veuillez remplir tous les champs obligatoires (*).";
    }
}
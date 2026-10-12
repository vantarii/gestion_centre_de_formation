<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Chargement des inscriptions pour le select
$inscriptions = [];
try {
    $inscriptions = $bdd->query('
        SELECT 
            i.id AS inscription_id,
            e.nom AS etudiant_nom,
            e.prenom AS etudiant_prenom,
            f.titre AS formation_titre
        FROM inscription i
        JOIN etudiant e ON i.etudiant_id = e.id
        JOIN formation f ON i.formation_id = f.id
        WHERE i.status = 1
        ORDER BY e.nom ASC
    ')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMsg = "Erreur lors du chargement des inscriptions : " . $e->getMessage();
}

// Récupération de l'identifiant (uuid prioritaire, id en repli)
$paramIdentifiant = $_GET['uuid'] ?? $_GET['id'] ?? $_POST['paiement_uuid'] ?? $_POST['id_paiement'] ?? null;

if (!empty($paramIdentifiant)) {
    $getPaiement = $bdd->prepare('SELECT * FROM paiement WHERE (paiement_uuid = ? OR id = ?)');
    $getPaiement->execute([$paramIdentifiant, $paramIdentifiant]);

    if ($getPaiement->rowCount() > 0) {
        $paiement = $getPaiement->fetch(PDO::FETCH_ASSOC);
        $idPaiement = $paiement['id'];

        // Si le paiement n'a pas encore d'UUID, on lui en génère un
        if (empty($paiement['paiement_uuid'])) {
            $newUuid = generateRandomString(6);
            $updateUuid = $bdd->prepare('UPDATE paiement SET paiement_uuid = ? WHERE id = ?');
            $updateUuid->execute([$newUuid, $idPaiement]);
            $paiement['paiement_uuid'] = $newUuid;
        }
        $paiementUuid = $paiement['paiement_uuid'];

    } else {
        $errorMsg = "Paiement introuvable.";
    }
} else {
    $errorMsg = "Aucun identifiant transmis.";
}

// 2. Traitement de la modification lors de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    $inscriptionId = !empty($_POST['inscription_id']) ? intval($_POST['inscription_id']) : null;
    $montant = !empty($_POST['montant']) ? floatval($_POST['montant']) : null;
    $modePaiement = !empty($_POST['mode_paiement']) ? trim(htmlspecialchars($_POST['mode_paiement'])) : null;
    $reference = !empty($_POST['reference']) ? trim(htmlspecialchars($_POST['reference'])) : null;
    $datePaiement = !empty($_POST['date_paiement']) ? $_POST['date_paiement'] : date('Y-m-d H:i:s');
    $statut = isset($_POST['statut']) ? intval($_POST['statut']) : 1;

    if (isset($idPaiement) && $inscriptionId && $montant && $modePaiement && $reference) {
        try {
            $updatePaiement = $bdd->prepare('
                UPDATE paiement 
                SET inscription_id = ?, montant = ?, date_paiement = ?, mode_paiement = ?, reference = ?, statut = ?
                WHERE id = ?
            ');
            $updatePaiement->execute([
                $inscriptionId,
                $montant,
                $datePaiement,
                $modePaiement,
                $reference,
                $statut,
                $idPaiement
            ]);

            $_SESSION['success_msg'] = "Le paiement a été mis à jour avec succès.";
            header('Location: index.php');
            exit();

        } catch (PDOException $e) {
            $errorMsg = "Erreur SQL lors de la modification : " . $e->getMessage();
        }
    } else {
        $errorMsg = "Tous les champs obligatoires doivent être remplis.";
    }
}
<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/signupTrait.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<body>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <h2 class="mb-4 text-center">Inscription Gérante</h2>

                <!-- Affichage des erreurs ou confirmations -->
                <?php if (isset($errorMsg)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $errorMsg; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($successMsg)): ?>
                    <div class="alert alert-success" role="alert">
                        <?= $successMsg; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?= isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="firstname" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?= isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"  required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" name="validate">
                        S'inscrire
                    </button>

                    <div class="text-center">
                        <a href="login.php" class="text-decoration-none">
                            J'ai déjà un compte, je me connecte
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>

</body>
</html>
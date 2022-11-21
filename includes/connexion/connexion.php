<?php
session_start();
if (!isset($_SESSION["user"])) {
    // header("Location: connexion.php");
    // exit;
}

?>

<?php
$title = "Accueil";
require_once "includes/header.php"; ?>

<link rel="stylesheet" href="ConnexionStyle.css">
<form action="" method="post">
<input type="email" name="email" id="email" placeholder="Adresse de messagerie" required>
<input type="password" name="pass" id="pass" placeholder="Mot de passe" required>
<button type="submit" class="btn-confirm">Me connecter</button>
</form>

<?php require_once "includes/footer.php"; ?>

<?php
session_start();
if (!isset($_SESSION["user"])) {
    // header("Location: connexion.php");
    // exit;
}

if (!empty($_POST)) {
    if (isset($_POST["email"], $_POST["pass"]) && !empty($_POST["email"]) && !empty($_POST["pass"])) {
        $_SESSION["error"] = [];
        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error"][] = "Adresse email ou mot de passe incorrect";
        }
        if ($_SESSION["error"] === []) {
            require "includes/connect.php";
            $sql = "SELECT * FROM `membre` WHERE `mail` = :email";
            $query = $db->prepare($sql);
            $query->bindValue(":email", $_POST["email"], PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch();

            if (!$user) {
                $_SESSION["error"][] = "Utilisateur ou mot de passe incorrect";
            } elseif (!password_verify($_POST["pass"], $user["pass"])) {
                $_SESSION["error"][] = "Utilisateur ou mot de passe incorrect";
            }
            if ($_SESSION["error"] === []) {
                $_SESSION["user"] = [
                    "id" => $user["id"],
                    "name" => $user["pseudo"],
                    "lastname" => $user["mail"],
                ];
                header("Location: index.php");
            }
        }
    }
}
?>
<?php
$title = "Connexion";
require_once "includes/header.php"; ?>
<h2>Connexion</h2>
<form action="" method="post">
    <div>
        <label for="email">Identifiants</label>
        <input type="email" name="email" id="email" placeholder="Adresse de messagerie" required>
    </div>
    <div>
        <label for="email">Mot de passe</label>
        <input type="password" name="pass" id="pass" placeholder="Mot de passe" required>
    </div>
    <div class="btn">
        <button type="submit" class="btn-confirm">Se connecter</button>
    </div>
</form>

<?php require_once "includes/footer.php"; ?>
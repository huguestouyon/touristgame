<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}
if (!empty($_POST)) {
    if (isset($_POST["email"], $_POST["pseudo"], $_POST["pass"], $_POST["pass2"]) && !empty($_POST["email"]) && !empty($_POST["pseudo"]) && !empty($_POST["pass"]) && !empty($_POST["pass2"])) {
        $_SESSION["error"] = [];
        $pseudo = strip_tags($_POST["pseudo"]);
        if (strlen($pseudo) < 2) {
            $_SESSION["error"][] = "Le pseudo est trop court";
        }
        if (strlen($_POST["pass"] < 4)) {
            $_SESSION["error"][] = "Le mot de passe est trop court";
        }
        // Mot de passe différents
        if ($_POST["pass"] !== $_POST["pass2"]) {
            $_SESSION["error"][] = "Les mots de passes saisis ne sont pas identiques";
        }
        // Filtrage de l'email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error"][] = "L'adresse email est incorrecte";
        }
        if ($_SESSION["error"] === []) {
            // Hasher le pass
            $pass = password_hash($_POST["pass"], PASSWORD_ARGON2ID);
            // Se connecter à la base de données
            require_once "includes/connect.php";
            // email unique
            $email = strtolower($_POST["email"]);
            $sql = "SELECT * FROM `membre` WHERE mail = ?";
            $query = $db->prepare($sql);
            $query->bindValue(1, $email, PDO::PARAM_STR);
            $query->execute();
            $verifmail = $query->fetch();
            if ($verifmail) {
                $_SESSION["error"] = ["L'email est déjà utilisé"];
            }
            if ($_SESSION["error"] === []) {
                $sql = "INSERT INTO `membre`(`pseudo`, `mail`, `pass`) VALUES (:pseudo, :mail, :pass)";
                $query = $db->prepare($sql);
                $query->bindValue(":pseudo", $pseudo, PDO::PARAM_STR);
                $query->bindValue(":mail", $email, PDO::PARAM_STR);
                $query->bindValue(":pass", $pass, PDO::PARAM_STR);
                $query->execute();
                $id = $db->lastInsertId();
                $_SESSION["user"] = [
                    "id" => $id,
                    "email" => $email,
                    "pseudo" => $pseudo
                ];
                header("Location: index.php");
            }
        }
    } else {
        $_SESSION["error"] = ["Des valeurs sont manquantes"];
    }
}
?>


<?php
$title = "Accueil";
require_once "includes/header.php"; ?>

<form action="" method="post">

    <input type="email" name="email" id="email" placeholder="Adresse de messagerie" required>
    <input type="text" name="pseudo" id="pseudo" placeholder="Pseudo" required>
    <input type="password" name="pass" id="pass" placeholder="Mot de passe" required>
    <input type="password" name="pass2" id="pass" placeholder="Confirmation de Mot de passe" required>
    <button type="submit" class="btn-confirm">Me connecter</button>
</form>

<?php require_once "includes/footer.php"; ?>
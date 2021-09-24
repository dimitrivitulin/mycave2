<?php
// Initialise la session
session_start();

// Vérifie si l'utilisateur est déjà connecté, si oui redirige-le vers la page d'accueil
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

// Include config php
require_once "config.php";

// Défini des variables et initialise avec des valeurs vides
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Traitement des données du formulaire lors de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // vérifie si username est vide
    if (empty(trim($_POST["username"]))) {
        $username_err = "S'il vous plaît entrez votre identifiant.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Vérifie si password est vide
    if (empty(trim($_POST["password"]))) {
        $password_err = "S'il vous plaît entrez votre mot de passe .";
    } else {
        $password = trim($_POST["password"]);
    }

    // Valider les identifiants
    if (empty($username_err) && empty($password_err)) {
        // Déclaration SQL
        $sql = "SELECT id, username, password FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            // Lier des variables à l'instruction préparée en tant que paramètres
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

            // Défini les paramètres
            $param_username = trim($_POST["username"]);

            // Tentative d'execution de l'instruction préparé
            if ($stmt->execute()) {
                // Vérifie si le nom de l'utilisateur existe si oui vérifie le mdp
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if (password_verify($password, $hashed_password)) {
                            // Si le mdp est correct ouvrir une session
                            session_start();

                            // Stocke les données dans des variables de session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Redirige l'utilisateur à la page welcome
                            header("location: welcome.php");
                        } else {
                            // Si le le mdp est invalide affiche un message d'erreur
                            $login_err = "Identifiant ou mot de passe invalide";
                        }
                    }
                } else {
                    // Si l'identifiant n'existe pas, écrit un message d'erreur
                    $login_err = "Identifiant ou mot de passe invalide.";
                }
            } else {
                echo "Quelque chose s'est mal passé veuillez reessayer";
            }

            // Ferme la déclaration
            unset($stmt);
        }
    }

    // Ferme la connection
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="./assets/css/style.css">

</head>

<body>
    <div class="container-login flex-center">
        <div class="logo flex-center">
            <img src="./assets/img/logo.jpg" alt="logo my cave">
        </div>
        <div class="ligne-blanche"></div>
            <h2 class="titre-connect">Connexion</h2>
            <p>Veuillez renseigner vos identifiants pour vous connecter.</p>

            <?php
            if (!empty($login_err)) {
                echo '<div>' . $login_err . '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form-login flex-center">
                <div class="form-group flex-center">
                    <label>Identifiant</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group flex-center">
                    <label>Mot de passe</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
               
                    <button type="submit" class="btn-login">Se connecter</button>

            </form>
                <div class="ligne-blanche"></div>
               <a href="register.php" class="inscription-btn">Inscription</a>
        
    </div>
</body>

</html>
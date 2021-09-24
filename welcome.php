<?php
// Initialise une session
session_start();
require_once "config.php";

// Vérifie si l'utilisateur est connecté, sinon redirige-le vers la page login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <main class= "accueil flex-center">

        <div class="reinitialisation flex-center">
            <p>Compte Utilisateur</p>
            <a href="reset-password.php"class="rmdp" >Réinitialisation mdp</a>
            <a href="logout.php" class="rlogout">Déconnexion</a>
       
        </div>
        <div class="bienvenue flex-center">
            <h1 class="titre">Bienvenue</h1>
            <h2 class="sous-titre"> <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>
            <p>My cave est une application dédiée aux passionnés de vin  afin de répertorier les vins qu'ils ont en leur possession dans leur cave personnelle. </p>
        </div>
        <div class="btn-ajouter">
            <a href="./formulaire-ajout.php" class="btn-creme">Ajouter un Vin</a>
        </div>
        <div class="ligne-bordeaux"></div>
        <div class="to-do-vin-section">
            
            <?php
            $mes_vins = $pdo->query(
           "SELECT * 
            FROM mes_vins
            ORDER BY id 
            DESC");
            if (isset($_GET['message']) && $_GET['message'] == 'erreur') {
                echo "<p>Il semble qu'il ait un problème réssayer</p>";
            }
            ?>
            <div class="les-vins">
            <h1 class="sous-titre">MA CAVE</h1>
                
                <?php while ($mon_vin = $mes_vins->fetch(PDO::FETCH_ASSOC)) { 
                    $imageVin = $mon_vin['image_vin'];
                    if($imageVin == NULL){
                        $imageVin = "vin_defaut.png";    
                    }else{
                        $imageVin = $mon_vin['image_vin'];   
                    } 
                    $id = $mon_vin['id'];
                    ?>
                      <div class="le-vin">
                          <div class="popup" id="popup-<?= $id ?>">
                              <div class="fenetre-popup flex-center">
                                  <a href="" class="cross">&times;</a>
                                  <p>Êtes-vous sûre de vouloir effacer cette bouteille?</p>
                                  <a href="./effacer.php?effacer=<?php echo $mon_vin['id']; ?>" class="effacer">Effacer</a>
                              </div>
                          </div>
                          <a  href="#popup" class="effacer-vin flex-center">&times;</a>
                            <div class="img-vin">
                                <div class="bulle-img flex-center">
                                    <img src="./assets/img/<?php echo $imageVin?>" width="100%">
                                </div>    
                            </div>
                            <div class="description-vin">
                                <h3><?php echo $mon_vin['titre']; ?></h3>

                                <h4>Millésime <?php echo $mon_vin['millesime']; ?></h4>

                                <p>Stock: <?php echo $mon_vin['stock']; ?></p>
                                <a href="afficher.php?id=<?php echo $mon_vin['id'];?>" class="lien-vin btn-bordeaux">Afficher plus</a>

                                <?php $date = $mon_vin['date']; ?>
                                <small>Ajouté le:<?php echo date('d/m/Y', strtotime($date)); ?> </small>

                            </div>
                      </div>
                   
                    
                <?php } ?>
                
        </div>
        
       
        
            
                    
       
    </main>
    <script src="./assets/js/script.js"></script>
</body>

</html>
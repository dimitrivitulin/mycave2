<head>
    <meta charset="UTF-8">
    <title>Afficher votre vin</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body class="afficher-box">
    <main class="box-vin">
        <?php 
            require_once "config.php";
            $id = intval($_GET['id']);
            function findWine(PDO $pdo, $id) 
            {  
                $query = $pdo->prepare("SELECT * 
                FROM mes_vins w
                INNER JOIN cepage
                ON cepage.id = w.cepage_id
                INNER JOIN pays
                ON pays.id = w.pays_id
                WHERE w.id = :id
                "); 
                $query->bindValue(':id', intval($id), PDO::PARAM_INT);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);
                if ($result) { 
                    return $result;
                }
                return false; 
            }

            $mon_vin = findWine($pdo, $id);
                    
            $imageVin = $mon_vin['image_vin'];
            if($imageVin == NULL){
                $imageVin = "vin_defaut.png";
            }else{
                $imageVin = $mon_vin['image_vin'];
            }

            if (empty($_GET['id']) || !isset($mon_vin['id'])) 
                header('Location: welcome.php');
            else
        ?>
        <div class="afficher-vin flex-center">
            <div class="afficher1">
                <div class="afficher-img">
                    <img src="assets/img/<?php echo trim($imageVin)?>" width= "100%" height="300px">
                </div>
                <div class="afficher-titres">
                    <h1><?php echo $mon_vin['titre'];?></h1>
                    <p><span>Millesime</span> <?php echo $mon_vin['millesime'];?></p>
                    <p><span>Cépage:</span> <?php echo $mon_vin['cepage_name'];?></p>
                    <p><span>Pays:</span> <?php echo $mon_vin['pays_name'];?></p>
                    <div class="afficher-stock flex-center">
                        <p><?php echo $mon_vin['stock'];?></p>
                    </div>
                </div>
            </div>
            <div class="afficher2 flex-center">
                <div class="description-vin-text">
                    <p><?php echo $mon_vin['description_vin'];?></p>
                </div>
                <div class="afficher-btn">
                    <a href="./formulaire-modif.php?id=<?php echo $id;?>">Modifier</a>
                </div>
            </div>
 
            <a href="welcome.php"  class="revenir-btn btn-bordeaux">Revenir aux bouteilles</a>
        </div>
    </main>
    <footer class="flex-center">
        <a href="./welcome.php"><img src="./assets/img/logo.jpg" alt="logo mycave"></a>
    </footer>
    <script src="./assets/js/script.js"></script>
</body>
</html>
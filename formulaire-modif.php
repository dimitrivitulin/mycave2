<?php
// Include config php
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Vin</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <main class="box-vin ">
        <?php 
    // Ici je déclare mes variables

    $id = $_GET['id'];
    $image_vin = isset($_FILES['image_vin']) ? $_FILES['image_vin'] : FALSE;
    $ext = array('png','jpg','jpeg','');
    $datejour = date('Y');
// Ici je vérifie si je récupère bien l'id du vin si oui je fais ma fonction
    if (isset($_GET['id'])){
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
                $mes_vins = findWine($pdo, $id);
                $imageVin = $mes_vins['image_vin'];
                if($imageVin == NULL){
                    $imageVin = "vin_defaut.png";
                }
            }else{
                header('Location: welcome.php'); 
            } 
        //Je creer ma fonction
        function updateWine(PDO $pdo, $mes_vins, $id) 
        {
            $sql = "UPDATE mes_vins 
            SET titre = :titre, 
            cepage_id = :cepage_id, 
            pays_id = :pays_id, 
            millesime = :millesime, 
            stock = :stock, 
            description_vin = :description_vin, 
            image_vin = :image_vin  
            WHERE id = :id";
            $prepare = $pdo->prepare($sql);
            return $prepare->execute(array( 
                'id' => $id,
                'description_vin' => $mes_vins['description_vin'],
                'titre' => $mes_vins['titre'],
                'cepage_id' => $mes_vins['cepage_id'],  
                'pays_id' => $mes_vins['pays_id'], 
                'millesime' => $mes_vins['millesime'],
                'stock' => $mes_vins['stock'],
                'image_vin' => $mes_vins['image_vin']   
            ));
        }          
        //ici je verifie si les valeurs ne sont pas vide dans le formulaire et si la valeur est diferente de celle lors du chargement de la page
        if (!empty($_POST['submit']) 
        || (!empty($_POST['titre']) && $_POST['titre'] != $mes_vins['titre']) 
        || (!empty($_POST['pays_id']) && $_POST['pays_id'] != $mes_vins['pays_id']) 
        || (!empty($_POST['millesime']) && $_POST['millesime'] != $mes_vins['millesime'])
        || (!empty($_POST['description_vin']) && $_POST['description_vin'] != $mes_vins['description_vin'])
        || (!empty($_POST['cepage_id']) && $_POST['cepage_id'] != $mes_vins['cepage_id'])
        || (!empty($_POST['stock']) && $_POST['stock'] != $mes_vins['stock'])
        || (!empty($_FILES['image_vin']) && $_FILES['image_vin'] != $mes_vins['image_vin'])
        )  {

            //         //je gere le changement d'image:
            if ($_FILES['image_vin']['error'] === 0){
                $img_name = uniqid() . '.' . (pathinfo($_FILES['image_vin']['name'], PATHINFO_EXTENSION));
                @mkdir(__DIR__ . '/assets/img/', 0775);
                $img_folder = __DIR__ . '/assets/img/';
                $dir = $img_folder . $img_name;
                $move_file = @move_uploaded_file($_FILES['image_vin']['tmp_name'], $dir);
                if($move_file){
                    $mes_vins['image_vin'] = $img_name;
                }else{
                    echo 'Une erreur s\'est produite lors du telechargement du fichier, merci de renouveler votre demande.';
                }
            }
            //   //comme l'utilisateur peut poster un vin sans description je fais une verif
            if (!empty($_POST['description_vin'])){
              $desc = htmlentities($_POST['description_vin'], ENT_QUOTES);
              }else{
                  $desc = NULL;
              }
            
              $mes_vins = array(
                  'description_vin' => $desc,
                  'titre'           => htmlentities($_POST['titre'], ENT_QUOTES),
                  'cepage_id'       => intval($_POST['cepage_id']),
                  'pays_id'         => intval($_POST['pays_id']),
                  'millesime'       => intval($_POST['millesime']),
                  'stock'           => intval($_POST['stock']),
                  'image_vin'       => $mes_vins['image_vin']
              );


              if (updateWine( $pdo, $mes_vins, $id))
                  header("Location: afficher.php?id= $id ");
              else
                  echo 'Erreur lors de la modification !';       
          }                  
            ?>
                    <div class="ligne-bordeaux"></div>
        <h1>Carte d'identité du vin</h1>
        <p>Modifier votre vin</p>
        <form action="" method="POST" enctype="multipart/form-data" class="form-modif flex-center">                          
            <input type="text" name="titre" value="<?php echo $mes_vins['titre'];?>" class="input-titre">
            <p>Modifier Cépage</p>
            <select  name="cepage_id" class="input-titre">
                <option value="<?php echo $mes_vins['cepage_id'];?>"><?php echo $mes_vins['cepage_name'];?></option>
                    <?php
                        function selectCepage ($pdo){
                            $sql = "SELECT *
                            FROM cepage 
                            ORDER BY id 
                            DESC"; 
                            $query = $pdo->query($sql);
                            return $query->fetchAll(PDO::FETCH_ASSOC);   
                        }  
                        $cepage = selectCepage ($pdo);
                        foreach ($cepage as $cep) {
                            echo '<option value="' . $cep['id'] . '">' . $cep['cepage_name'] . '</option>';                 
                        }
                    ?>
            </select>
            <p>Modifier Pays</p>
            <select  name="pays_id" class="input-titre">
                <option value="<?php echo $mes_vins['pays_id'];?>"><?php echo $mes_vins['pays_name'];?></option>
                    <?php
                        function selectPays ($pdo){
                            $sql = "SELECT *
                            FROM pays 
                            ORDER BY id 
                            DESC"; 
                            $query = $pdo->query($sql);
                            return $query->fetchAll(PDO::FETCH_ASSOC);   
                        }                       
                        $pays = selectPays ($pdo);
                        foreach ($pays as $pays) {
                            echo '<option value="' . $pays['id'] . '">' . $pays['pays_name'] . '</option>';                        
                        }
                    ?>
            </select>
            <div class="form-millesime">
                <label for="millesime">Millesime</label>    
                <input type="text" name="millesime" value="<?php echo $mes_vins['millesime'];?>">
            </div>
            <div class="form-stock">
                <label for="stock">Stock</label>
                <input type="number" name="stock" value="<?php echo $mes_vins['stock'];?>">
            </div>
            <div class="modif-image-form flex-center">
                <div class="img-image-form flex-center">
                    <img src="assets/img/<?php echo trim($imageVin)?>" width= "100%" height="300px">                
                </div>
                <div class="infos-modif-img flex-center">
                    <label for="image_vin" class="btn-creme">Modifier image</label>
                    <input type="file" name="image_vin" class=" ajout-image-input" id="image_vin" class="modif-image-input">
                </div>
            </div>           
            <div class="ligne-bordeaux center-self"></div>
            <h2 class="center-self sous-titre">Caractéristiques</h2>
            <textarea name="description_vin" class="center-self form-description description-vin-text" ><?php echo $mes_vins['description_vin'];?></textarea>
            <div class="ligne-bordeaux center-self"></div>
            <input type="submit" name="submit" value="Modifier" class="center-self btn-creme">
        </form>                           
        <a href="welcome.php" class="revenir-btn btn-bordeaux">Revenir aux bouteilles</a>
    </main>
    <footer class="flex-center">
        <img src="./assets/img/logo.jpg" alt="logo mycave">
    </footer>
    <script src="./assets/js/script.js"></script>
</body>
</html>
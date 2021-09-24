<?php
// Include config php
require_once "./config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Vin</title>

    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <main class="box-vin">
        <div class="ligne-bordeaux"></div>
        <h1 class="titre">Carte d'identité du vin</h1>
        <p>Entrez les caractéristiques de votre vin</p>
        <form action="" method="POST" enctype="multipart/form-data"  class="form-box-vin flex-center">
            <input type="text" name="titre" placeholder="Nom de château, domaine, vin..." class="input-titre">
            <select  name="cepage_id" class="input-titre">
                <option value="0">Cépage*</option>
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
            <select  name="pays_id" class="input-titre">
                <option value="0">Pays*</option>
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
                <label for="millesime">Millesime*</label>    
                <input type="text" name="millesime" id="millesime" placeholder="XXXX">
            </div>
            <div class="form-stock">
                <label for="stock">Stock*</label>
                <input type="number" name="stock" id="stock" placeholder="0">
            </div>
            <div class="ajout-image center-self">
                <p>Importer une image (facultatif)</p>
                <label for="image_vin" name="image_vin" class="btn-creme">Importer</label>
                <input type="file" name="image_vin" class=" ajout-image-input" id="image_vin">
            </div>
                <div class="ligne-bordeaux center-self"></div>
                <h2 class="sous-titre">Caractéristiques</h2>
                <textarea name="description_vin" class="center-self form-description description-vin-text" placeholder="Description du vin...(veuillez saisir plus de 10 caractères)"></textarea>
                <div class="ligne-bordeaux center-self"></div>
                <input type="submit" value="Ajouter" class="center-self btn-creme" > 
        </form>         
          
        <a href="./welcome.php" class="revenir-btn btn-bordeaux">Revenir aux bouteilles</a>   
    </main>
    <footer class="flex-center">
        <a href="./welcome.php"><img src="./assets/img/logo.jpg" alt="logo mycave"></a>
    </footer>                
    <?php
                //Je crée ma fonction
                function addWine($pdo, $mes_vins) 
                {
                    $sql = "INSERT INTO mes_vins (description_vin, titre, cepage_id, pays_id, millesime, stock,  image_vin)
                    VALUES (:description_vin, :titre, :cepage_id, :pays_id, :millesime, :stock, :image_vin)";
                    $prepare = $pdo->prepare($sql);
                    return $prepare->execute(array( 
                        'description_vin' => isset($mes_vins['description_vin']) ? $mes_vins['description_vin'] : NULL,
                        'titre' => $mes_vins['titre'],
                        'cepage_id' => intval($mes_vins['cepage_id']),  
                        'pays_id' => intval($mes_vins['pays_id']), 
                        'millesime' => intval($mes_vins['millesime']),
                        'stock' => intval($mes_vins['stock']),
                        'image_vin' => isset($mes_vins['image_vin']) ? $mes_vins['image_vin'] : NULL
                    ));
                }
               $image_vin = isset($_FILES['image_vin']) ? $_FILES['image_vin'] : FALSE;
               
               $ext = array('png','jpg','jpeg','');
               $datejour = date('Y');
               //Si ca retourne true; exit
               $isInputInvalid =  empty($_POST['titre']) || empty($_POST['pays_id']) || empty($_POST['millesime']) || empty($_POST['cepage_id']); 
               if ($isInputInvalid){
                exit('Tous les champs marqués d\'un * sont obligatoires !');
               }
               $fileInvalid = 
               ($image_vin['error'] === 1 || $image_vin['error'] === 2 || $image_vin['error'] === 3 || $image_vin['size'] > 1000000) && (!in_array(pathinfo($image_vin['name'], PATHINFO_EXTENSION), $ext) && $image_vin['error'] === 0 ) ;
               if ($fileInvalid){
                 exit('Le fichier doit faire max 1Mo et doit être au format jpeg, png ou jpg.');
               }
               //buildWineArray; construit le array avec des condition;
                $buildWineArray = array();
                $buildWineArray ['titre']      = htmlentities(trim($_POST['titre']), ENT_QUOTES);
                $buildWineArray ['pays_id']      = htmlentities($_POST['pays_id'], ENT_QUOTES);
                if ($_POST ['millesime'] < 1900) {
                    exit ('Désolé, passé un certain temps votre vin n\'est plus un vin mais du vinaigre. Veuillez saisir une date valide.');
                }elseif($_POST ['millesime'] > $datejour) {
                    exit('Désolé, la vendange n\'a pas eu lieu. Veuillez saisir une date valide.');
                }else{
                    $buildWineArray ['millesime'] = htmlentities($_POST['millesime'], ENT_QUOTES);
                }
                $buildWineArray ['cepage_id'] = htmlentities($_POST['cepage_id'], ENT_QUOTES);
                $buildWineArray ['stock'] = htmlentities($_POST['stock'], ENT_QUOTES);
                if (!empty($_POST['description_vin'])){
                    $buildWineArray ['description_vin'] = htmlentities($_POST['description_vin'], ENT_QUOTES);
                } 
                if ($image_vin['error'] === 0){
                    $img_name = uniqid() . '.' . (pathinfo($image_vin['name'], PATHINFO_EXTENSION));
                    @mkdir(__DIR__ . '/assets/img/', 0775);
                    $img_folder = __DIR__ . '/assets/img/';
                    $dir = $img_folder . $img_name;
                    $move_file = @move_uploaded_file($image_vin['tmp_name'], $dir);
                    if($move_file){
                        $buildWineArray ['image_vin'] = $img_name;
                    }else{
                        exit('Une erreur s\'est produite lors du telechargement du fichier, merci de renouveler votre demande.');
                    }
                }
                $mes_vins = $buildWineArray;
                // execute la fonction addWine;
                // redirige vers la liste si tout c'est bien passé;
                addWine($pdo, $mes_vins);
                header('Location: welcome.php');            
            ?>  
    <script src="./assets/js/script.js"></script>
</body>

</html>
<?php
$bdd = new PDO('mysql:host=localhost;dbname=phpTd', 'phpTd', '');
// On détermine sur quelle page on se trouve
if(isset($_GET['page']) && !empty($_GET['page'])){
    $currentPage = (int) strip_tags($_GET['page']);
}else{
    $currentPage = 1;
}

$create = 'CREATE TABLE IF NOT EXISTS images( 
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(15) NOT NULL,
  type VARCHAR(15) NOT NULL,
  size VARCHAR(10) NOT NULL,
  path VARCHAR(255) NOT NULL
);';

$bdd->exec($create);

$parPage = 4;

$sql = 'SELECT COUNT(*) AS nb_images FROM `images`;';
$query = $bdd->prepare($sql);
$query->execute();
$result = $query->fetch();
$nbimages = (int) $result['nb_images'];
$pages = ceil($nbimages / $parPage);

$premier = $parPage*($currentPage - 1);

$sql = 'SELECT * FROM `images` LIMIT :premier, :parpage;';
$query = $bdd->prepare($sql);

$query->bindValue(':premier', $premier, PDO::PARAM_INT);
$query->bindValue(':parpage', $parPage, PDO::PARAM_INT);
$query->execute();
$curentimage= $query->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
   <body>
      <form method="post" action="" enctype="multipart/form-data">
         <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
         Envoyez ce fichier :
         <input type="file" name="monfichier" /><br />
         <input type="submit" name="valider" value="Uploader" />
         Seules les fichiers du types .jpeg .png sont acceptés de taille maximale de 8Mo !
      </form>
      <br>
      <div class="row">
            <section class="col-12">
                <h1>Liste des images</h1>
                <br>
                <table class="table">
                    <thead>
                        <th>id</th>
                        <th>name</th>
                        <th>type</th>
                        <th>size</th>
                        <th>path</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach($curentimage as $image){
                        ?>
                            <tr>
                                <td><?= $image['id']  ?></td>
                                <td><?= $image['name'] ?></td>
                                <td><?= $image['type']  ?></td>
                                <td><?= $image['size']  ?></td>
                                <td><img src="https://github.com/alihaydar8/introduction-aux-humanit-s-numeriques-et-m-thodes-digitales/blob/master/tp1/pagination.png" width="100" height="100"></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <img src="pagination.png" width="100" height="100">
                <nav>
                    <ul class="pagination">
                        <!-- Lien vers la page précédente (désactivé si on se trouve sur la 1ère page) -->
                        <li class="page-item <?= ($currentPage == 1) ? "disabled" : "" ?>">
                            <a href="/tp2/?page=<?= $currentPage - 1 ?>" class="page-link">Précédente</a>
                        </li>
                        <?php for($page = 1; $page <= $pages; $page++): ?>
                          <!-- Lien vers chacune des pages (activé si on se trouve sur la page correspondante) -->
                          <li class="page-item <?= ($currentPage == $page) ? "active" : "" ?>">
                                <a href="/tp2/?page=<?= $page ?>" class="page-link"><?= $page ?></a>
                            </li>
                        <?php endfor ?>
                          <!-- Lien vers la page suivante (désactivé si on se trouve sur la dernière page) -->
                          <li class="page-item <?= ($currentPage == $pages) ? "disabled" : "" ?>">
                            <a href="/tp2/?page=<?= $currentPage + 1 ?>" class="page-link">Suivante</a>
                        </li>
                    </ul>
                </nav>
            </section>
        </div>
   </body>
</html>
<?php
   if(isset($_POST["valider"])){
      if(!preg_match("#jpeg|png#",$_FILES["monfichier"]["type"]))
         echo '<span>Format invalide!</span>';
      elseif($_FILES["monfichier"]["size"]>8000000)
         echo '<span class="nook">Taille trop grande!</span>';
      else{


        $sql = "INSERT INTO images (name,type,size,path) VALUES(:name,:type,:size,:path)";
    
        $statement = $bdd->prepare($sql);
        $str ="images/";
        $str.= $_FILES["monfichier"]["name"];
        $statement->execute([
            ':name' => $_FILES["monfichier"]["name"],
            ':type' => $_FILES["monfichier"]["type"],
            ':size' => $_FILES["monfichier"]["size"],
            ':path' => $str,
        ]);
         if(move_uploaded_file($_FILES["monfichier"]["tmp_name"],"images/".$_FILES["monfichier"]["name"])){
            echo '<span class="ok">Image chargée avec succès</span>';
            header("Refresh:0"); 
          }
      }
   }
?>


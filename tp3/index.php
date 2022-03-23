<?php
function explorer($chemin,$bdd){
    $lstat    = lstat($chemin);
    $path_parts = pathinfo($chemin);
    $filetype = filetype($chemin);
    // echo '<pre>'; var_dump($lstat); echo '</pre>'; 
    // Affichage des infos sur le fichier $chemin
    if($filetype=="file" && $lstat['size'] <8000000 && ($path_parts['extension'] =='png' || $path_parts['extension'] =='jpg') ){
        // echo "path :$chemin\t\ttype: $filetype \t\tsize: $lstat[size]\t\t $path_parts[extension]\t\t mtime: $mtime\n";
        $sql = "INSERT INTO docs (name,type,size,path) VALUES(:name,:type,:size,:path)";
        
        $statement = $bdd->prepare($sql);
        $statement->execute([
            ':name' => $path_parts['filename'],
            ':type' => $path_parts['extension'],
            ':size' => $lstat['size'],
            ':path' => $chemin,
        ]);
    }
    // Si $chemin est un dossier => on appelle la fonction explorer() pour chaque élément (fichier ou dossier) du dossier$chemin
    if( is_dir($chemin) ){
        $me = opendir($chemin);
        while( $child = readdir($me) ){
            if( $child != '.' && $child != '..' ){
                explorer( $chemin.DIRECTORY_SEPARATOR.$child,$bdd);
            }
        }
    }
}

$bdd = new PDO('mysql:host=localhost;dbname=phpTd', 'phpTd', '');
// On détermine sur quelle page on se trouve
if(isset($_GET['page']) && !empty($_GET['page'])){
    $currentPage = (int) ($_GET['page']);
}else{
    $currentPage = 1;
}

$create = 'CREATE TABLE IF NOT EXISTS docs( 
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    size VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL
);';
$bdd->exec($create);

$sql = 'SELECT COUNT(*) AS nb_docs FROM `docs`;';
$query = $bdd->prepare($sql);
$query->execute();
$result = $query->fetch();
if($result['nb_docs'] == 0)
{
    header('Content-type: text/plain');
    explorer("./docs",$bdd);
    $query->execute();
    $result = $query->fetch();
}
$parPage = 8;

$nbdocs = (int) $result['nb_docs'];
$pages = ceil($nbdocs / $parPage);

$premier = $parPage*($currentPage - 1);

$sql = 'SELECT * FROM `docs` ORDER BY id DESC LIMIT :premier, :parpage;';
$query = $bdd->prepare($sql);

$query->bindValue(':premier', $premier, PDO::PARAM_INT);
$query->bindValue(':parpage', $parPage, PDO::PARAM_INT);
$query->execute();
$curentdoc= $query->fetchAll(PDO::FETCH_ASSOC);
$msg = "";
if(isset($_POST["valider"])){

    $str ="images/";
    $str.= $_FILES["fichier"]["name"];
    if(!preg_match("#jpeg|png#",$_FILES["fichier"]["type"]))
        $msg = '<span style="color:red">Format invalide!</span>';
    elseif($_FILES["fichier"]["size"]>8000000)
        $msg = '<span style="color:red">Taille trop grande!</span>';
    elseif(file_exists($str)){
        $msg ='<span style="color:red">image existe déja dans la base !</span>';
    }
    else{
        $sql = "INSERT INTO docs (name,type,size,path) VALUES(:name,:type,:size,:path)";
        $statement = $bdd->prepare($sql);
        $statement->execute([
            ':name' => $_FILES["fichier"]["name"],
            ':type' => $_FILES["fichier"]["type"],
            ':size' => $_FILES["fichier"]["size"],
            ':path' => $str,
        ]);
        if(move_uploaded_file($_FILES["fichier"]["tmp_name"],"images/".$_FILES["fichier"]["name"])){
            header("Refresh:1"); 
            $msg = '<span style="color:lime">Image chargée avec succès</span>';
        }
    }
}
?>

<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <style>
            .zoom {
            transition: transform .2s;
            }
            .zoom:hover {
            transform: scale(1.5); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
            }
        </style>
    </head>
    <body>
        <br>
        <div align="center">
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
                Upload une nouvelle photo :
                <input type="file" name="fichier" /><br />
                <input type="submit" name="valider" value="Uploader" />
                Seules les fichiers du types .jpeg .png sont acceptés de taille maximale de 8Mo !
            </form>
            <br>
            <?php echo $msg;?>
        </div>
        <?php if($curentdoc){ ?>
            <section class="section-products">
                <div>
                    <h1 align="center" >listes des fruits</h1>
                    <br>
                    <div class="row">
                    <!-- Single Product -->
                        <?php foreach($curentdoc as $doc){ ?>
                            <div class="col-md-8 col-lg-4 col-xl-3">
                                <div align="center" >
                                    
                                        <h4 class="product-title">name: <?= $doc['name'] ?></h3>
                                        <div class="zoom">
                                            <img src="<?= $doc['path']  ?>" width="200" height="190" style=""></li>
                                        </div>
                                        <h4 class="product-old-price">extention: <?= $doc['type']  ?></h4>
                                        <h4 class="product-price"> size: <?= $doc['size']  ?></h4>
                                        <br>
                                    
                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div>  
                <nav >
                    <ul  class="pagination justify-content-center">    
                        <!-- Lien vers la page précédente (désactivé si on se trouve sur la 1ère page) -->
                        <li class="page-item <?= ($currentPage == 1) ? "disabled" : "" ?>">
                            <a href="/tp3/?page=<?= $currentPage - 1 ?>" class="page-link" >&laquo;</a>
                        </li>
                        <?php for($page = 1; $page <= $pages; $page++): ?>
                        <!-- Lien vers chacune des pages (activé si on se trouve sur la page correspondante) -->
                            <li class="page-item <?= ($currentPage == $page) ? "active" : "" ?>">
                                <a href="/tp3/?page=<?= $page ?>" class="page-link"><?= $page ?></a>
                            </li>
                        <?php endfor ?>
                        <!-- Lien vers la page suivante (désactivé si on se trouve sur la dernière page) -->
                        <li class="page-item <?= ($currentPage == $pages) ? "disabled" : "" ?>">
                            <a href="/tp3/?page=<?= $currentPage + 1 ?>" class="page-link">&raquo;</a>
                        </li>
                    </ul>
                </nav>
                <br/>
            </section>
        <?php } ?>
   </body>
</html>
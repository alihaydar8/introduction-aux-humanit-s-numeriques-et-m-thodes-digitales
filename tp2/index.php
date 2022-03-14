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
        name VARCHAR(30) NOT NULL,
        type VARCHAR(30) NOT NULL,
        size VARCHAR(30) NOT NULL,
        path VARCHAR(255) NOT NULL
    );';
    $bdd->exec($create);

    $parPage = 2;

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

            $sql = "INSERT INTO images (name,type,size,path) VALUES(:name,:type,:size,:path)";
        
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
    </head>
    <body>
        <br/>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
            Envoyez ce fichier :
            <input type="file" name="fichier" /><br />
            <input type="submit" name="valider" value="Uploader" />
            Seules les fichiers du types .jpeg .png sont acceptés de taille maximale de 8Mo !
        </form>
        <?php echo $msg;?>
        <br>
        <?php if($curentimage){ ?>
            <div class="row">
                <section class="col-12">
                    <h1 align="center">Liste des images</h1>
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
                            <?php foreach($curentimage as $image){ ?>
                                <tr>
                                    <td><?= $image['id']  ?></td>
                                    <td><?= $image['name'] ?></td>
                                    <td><?= $image['type']  ?></td>
                                    <td><?= $image['size']  ?> Octet </td>
                                    <td><img src="<?= $image['path']  ?>" width="200" height="200"></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <nav >
                        <ul  class="pagination justify-content-center">    
                            <!-- Lien vers la page précédente (désactivé si on se trouve sur la 1ère page) -->
                            <li class="page-item <?= ($currentPage == 1) ? "disabled" : "" ?>">
                                <a href="/tp2/?page=<?= $currentPage - 1 ?>" class="page-link" >&laquo;</a>
                            </li>
                            <?php for($page = 1; $page <= $pages; $page++): ?>
                            <!-- Lien vers chacune des pages (activé si on se trouve sur la page correspondante) -->
                                <li class="page-item <?= ($currentPage == $page) ? "active" : "" ?>">
                                    <a href="/tp2/?page=<?= $page ?>" class="page-link"><?= $page ?></a>
                                </li>
                            <?php endfor ?>
                            <!-- Lien vers la page suivante (désactivé si on se trouve sur la dernière page) -->
                            <li class="page-item <?= ($currentPage == $pages) ? "disabled" : "" ?>">
                                <a href="/tp2/?page=<?= $currentPage + 1 ?>" class="page-link">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                </section>
            </div>
        <?php } ?>
   </body>
</html>

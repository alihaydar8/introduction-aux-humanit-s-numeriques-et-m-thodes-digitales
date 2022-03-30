<?php    
    session_start();
    $bdd = new PDO('mysql:host=localhost;dbname=phpTd', 'phpTd', '');
    
    // On détermine sur quelle page on se trouve
    if(isset($_GET['page']) && !empty($_GET['page'])){
        $currentPage = (int) ($_GET['page']);
    }else{
        $currentPage = 1;
    }
    $sql = 'SELECT COUNT(*) AS nb_docs FROM `docs`';
    $query = $bdd->prepare($sql);
    $query->execute();
    $result = $query->fetch();

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
?>

<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <style>
            /* pour zoomer sur les image */
            .zoom {
            transition: transform .2s;
            }
            .zoom:hover {
            transform: scale(1.5); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
            }
        </style>
    </head>
    <body>
        <!-- navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="./index.php">Home</a>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link" href="./scan.php">arborescence </a>
                    </li>
                </ul>
                <a class="nav-link "href="./login.php">Admin</a>
            </div>
        </nav>
        <br>
        <!-- afficher tous les images  -->
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
                                    
                                        <h4 class="product-title"><b>NAME:</b> <?= $doc['name'] ?></h3>
                                        <div class="zoom">
                                            <img src="<?= $doc['path']  ?>" width="200" height="190" style=""></li>
                                        </div>
                                        <h4 class="product-old-price"><b>EXTENTION:</b> <?= $doc['type']  ?></h4>
                                        <h4 class="product-price"><b>SIZE:</b> <?= $doc['size']  ?>oct</h4>
                                        <br>
                                    
                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div>  
                <nav >
                    <!-- pagination  -->
                    <ul  class="pagination justify-content-center">    
                        <!-- Lien vers la page précédente (désactivé si on se trouve sur la 1ère page) -->
                        <li class="page-item <?= ($currentPage == 1) ? "disabled" : "" ?>">
                            <a href="/tp4/?page=<?= $currentPage - 1 ?>" class="page-link" >&laquo;</a>
                        </li>
                        <?php for($page = 1; $page <= $pages; $page++): ?>
                        <!-- Lien vers chacune des pages (activé si on se trouve sur la page correspondante) -->
                            <li class="page-item <?= ($currentPage == $page) ? "active" : "" ?>">
                                <a href="/tp4/?page=<?= $page ?>" class="page-link"><?= $page ?></a>
                            </li>
                        <?php endfor ?>
                        <!-- Lien vers la page suivante (désactivé si on se trouve sur la dernière page) -->
                        <li class="page-item <?= ($currentPage == $pages) ? "disabled" : "" ?>">
                            <a href="/tp4/?page=<?= $currentPage + 1 ?>" class="page-link">&raquo;</a>
                        </li>
                    </ul>
                </nav>
                <br/>
            </section>
        <?php }else{ ?>

            <h1 align="center" >
                Data Base Empty
            </h1>


        <?php } ?>
   </body>
</html>
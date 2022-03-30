<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="./index.php">Home</a>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link" href="./scan.php">arborescence </a>
                    </li>
                </ul>
                <a class="nav-link" href="./login.php">Admin</a>
            </div>
        </nav>
        <div align="center" >
            <br>

            <form method="get" action="" enctype="multipart/form-data">
                <label for="name">Folder name for scan:</label>
                <input type="text" name="name"><br>
                <input type="submit" name="scan" value="Scan">
            </form>
        </div>
    </body>
</html>
<?php
// connextion a la base de donnée 
    $bdd = new PDO('mysql:host=localhost;dbname=phpTd', 'phpTd', '');
// fonction qui explore le fichier avec les sous fichiers d'une maniere recursive et sauvegarde les infos dans la base de donnée 
function explorer($chemin,$bdd){
    $lstat    = lstat($chemin);
    $path_parts = pathinfo($chemin);
    $filetype = filetype($chemin);
    if($filetype=="file" && $lstat['size'] <8000000 && ($path_parts['extension'] =='png' || $path_parts['extension'] =='jpg') ){
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
                if($child != "." && $child != ".." ){
                    explorer( $chemin.DIRECTORY_SEPARATOR.$child,$bdd);
                }
            }
        }
    }
    // fonction qui explore le fichier avec les sous fichiers d'une maniere recursive et les affichées sur l'écran.
    function explorer_and_display($chemin,$name,$niveau,$level=1){
        // Si $chemin est un dossier => on appelle la fonction explorer() pour chaque élément (fichier ou dossier) du dossier$chemin
        if( is_dir($chemin) ){
            $me = opendir($chemin);
            while( $child = readdir($me) ){
                if($child != "." && $child != ".." ){
                    $path_part = pathinfo($child);
                    if($level <= $niveau){
                        for($i=0; $i<=(6*$level); $i++) {
                            echo "&nbsp";
                        }
                        if(is_dir($chemin.DIRECTORY_SEPARATOR.$child)){
                            if($level == $niveau-1){
                                $lev =$level;
                            }
                            else{
                                $lev = $level+1;
                            }
                            echo "&#128193<a href='?name=$name&scan=Scan&niveau=$lev'>";
                            echo "$child <br></a>" ;
                        }
                        else{
                            $sizeinfo  = lstat($chemin.DIRECTORY_SEPARATOR.$child);
                            echo "<img src='https://img.icons8.com/color/30/000000/$path_part[extension].png'/>";
                            echo "<a href='$chemin/$child'>$child</a>&nbsp&nbsp&nbsp&nbspsize =  $sizeinfo[size] <br>";
                        } 
                    }
                    explorer_and_display( $chemin.DIRECTORY_SEPARATOR.$child,$name,$niveau,$level+1);
                }
            }
        }
    }
    ///  si on appuie sur le bouton
    if(isset($_GET["scan"])){
    /// efface la base 
        $drop = 'DROP TABLE if exists docs;';
        $bdd->exec($drop);
    /// Créer la base 
        $create = 'CREATE TABLE IF NOT EXISTS docs( 
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(255) NOT NULL,
            size VARCHAR(255) NOT NULL,
            path VARCHAR(255) NOT NULL
        );';

        $bdd->exec($create);
    //// sauvegarder dans la base
        explorer("./".$_GET['name'],$bdd);

        if(isset($_GET['niveau']) && !empty($_GET['niveau'])){
            $niv = $_GET['niveau'];
        }
        else {
            $niv = 1000;
        }
    /// affiche l'arborescence
        echo "&#128193<a href='?name=$_GET[name]&scan=Scan&niveau=1'>./</a><br>";
        explorer_and_display("./".$_GET['name'],$_GET['name'],$niv);
    }
?>

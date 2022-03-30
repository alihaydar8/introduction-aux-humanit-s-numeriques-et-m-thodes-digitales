<?php
// pour effacer l'image selectionné
session_start();
$bdd = new PDO('mysql:host=localhost;dbname=phpTd', 'phpTd', '');
if(isset($_GET['recordId']) && !empty($_GET['recordId'])){
    $req=$bdd->prepare(
        "DELETE FROM docs WHERE id=:id"
    );
    $req->bindValue(':id', $_GET['recordId'], PDO::PARAM_INT);
    $req->execute();
    header("Location: admin.php");
    exit();
}
?>
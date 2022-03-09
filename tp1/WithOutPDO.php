<?php
// On détermine sur quelle page on se trouve
if(isset($_GET['page']) && !empty($_GET['page'])){
    $currentPage = (int) strip_tags($_GET['page']);
}else{
    $currentPage = 1;
}


$texts = [["1",'a1'],["2",'b1'],["3",'c1'],["4",'d1'],["5",'e1'],["6",'f1'],["7",'g1'],["8",'h1'],["9",'i1'],["10",'j1'],["11",'k1'],["12",'l1'],["13",'m1'],["14",'n1'],["15",'o1'],["16",'p1'],["17",'q1'],["18",'r1'],["19",'s1'],["20",'t1'],["21",'u1'],["22",'v1'],["23",'w1'],["24",'x1'],["25",'y1'],["26",'z1'],["27",'a2'],["28",'b2'],["29",'c2'],["30",'d2'],["31",'e2'],["32",'f2'],["33",'g2'],["34",'h2'],["35",'i2'],["36",'j2'],["37",'k2'],["38",'l2'],["39",'m2'],["40",'n2'],["41",'o2'],["42",'p2'],["43",'q2'],["44",'r2'],["45",'s2'],["46",'t2'],["47",'u2'],["48",'v2'],["49",'w2'],["50",'x2'],["51",'y2'],["52",'z2'],["53",'a3'],["54",'b3'],["55",'c3'],["56",'d3'],["57",'e3'],["58",'f3'],["59",'g3'],["60",'h3'],["61",'i3'],["62",'j3'],["63",'k3'],["64",'l3'],["65",'m3'],["66",'n3'],["67",'o3'],["68",'p3'],["69",'q3'],["70",'r3'],["71",'s3'],["72",'t3'],["73",'u3'],["74",'v3'],["75",'w3'],["76",'x3'],["77",'y3'],["78",'z3'],["79",'a4'],["80",'b4'],["81",'c4'],["82",'d4'],["83",'e4'],["84",'f4'],["85",'g4'],["86",'h4'],["87",'i4'],["88",'j4'],["89",'k4'],["90",'l4'],["91",'m4'],["92",'n4'],["93",'o4'],["94",'p4'],["95",'q4'],["96",'r4'],["97",'s4'],["98",'t4'],["99",'u4'],["100",'v4'],["101",'w4'],["102",'x4'],["103",'y4'],["104",'z4']];

$nbtexts = sizeof($texts);

$parPage = 10;

$pages = ceil($nbtexts / $parPage);

$currenttexts = [];
for ($i = $parPage*($currentPage -1) , $j =0 ; $i < ($currentPage * $parPage) && $i<$nbtexts ; $i++ , $j++) {
    $currenttexts[$j] = $texts[$i];
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <h1>Liste des texts</h1>
                <br>
                <table class="table">
                    <thead>
                        <th>ID</th>
                        <th>text</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach($currenttexts as $text){
                        ?>
                            <tr>
                                <td><?= $text[0] ?></td>
                                <td><?= $text[1] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination">
                        <!-- Lien vers la page précédente (désactivé si on se trouve sur la 1ère page) -->
                        <li class="page-item <?= ($currentPage == 1) ? "disabled" : "" ?>">
                            <a href="/tp1/WithOutPDO.php/?page=<?= $currentPage - 1 ?>" class="page-link">Précédente</a>
                        </li>
                        <?php for($page = 1; $page <= $pages; $page++): ?>
                          <!-- Lien vers chacune des pages (activé si on se trouve sur la page correspondante) -->
                          <li class="page-item <?= ($currentPage == $page) ? "active" : "" ?>">
                                <a href="/tp1/WithOutPDO.php/?page=<?= $page ?>" class="page-link"><?= $page ?></a>
                            </li>
                        <?php endfor ?>
                          <!-- Lien vers la page suivante (désactivé si on se trouve sur la dernière page) -->
                          <li class="page-item <?= ($currentPage == $pages) ? "disabled" : "" ?>">
                            <a href="/tp1/WithOutPDO.php/?page=<?= $currentPage + 1 ?>" class="page-link">Suivante</a>
                        </li>
                    </ul>
                </nav>
            </section>
        </div>
    </main>
</body>
</html>
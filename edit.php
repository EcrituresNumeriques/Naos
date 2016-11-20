<?php
$configPassword = "dNh9h{E(\Qm5tB6>";
include('include/config.ini.php');
$siteName = "Accueil";
include("header.php");
?>
<table class="sortable">
   <thead>
       <th class="clickable">numero</th>
       <th class="clickable">entite</th>
           <th class="clickable">titre</th>
       <th class="clickable">auteur</th>
       <th class="clickable">langue</th>
       <th class="clickable">type</th>
   </thead>
    <tbody>



<?php
$getArticle = $bdd->prepare("SELECT id_texte, titre, auteur, type, lang, texte,id_entite FROM texte ORDER BY id_entite DESC");
$getArticle->execute() or die("Impossible de récupérer la liste d'article.");
$i = 0;
while($row = $getArticle->fetch()){
    $i++;
    echo('<tr><td>'.$i.'</td><td>'.$row['id_entite'].'</td><td><a href="showalign.php?alignee='.$row['id_entite'].'" target="frame'.$row['id_entite'].'">Titre : '.$row['titre'].'</a></td><td>'.$row['auteur'].'</td><td>'.$row['lang'].'</td><td>'.$row['type'].'</td></tr>');
}
//contenus de la page
?>

    </tbody>
</table>

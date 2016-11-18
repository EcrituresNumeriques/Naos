<?php
$configPassword = "dNh9h{E(\Qm5tB6>";
include('config.ini.php');
if($_POST['action'] != "traduction" OR $_POST['action'] == "traduction"){
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Traduction alignée</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <script src="jquery-2.1.3.min.js"></script>
    </head>
    <body>
        <form action="add.php" method="post">
            <input type="hidden" value="traduction" name="action">
            <select name="entite">
                <option value="new">Nouvelle entitée</option>
                <?php
    $getTextes = $bdd->prepare("SELECT * FROM `texte` order by id_entite");
    $getTextes->execute() or die('Erreur dans la recupération des textes');
    while($textes = $getTextes->fetch()){
        echo('<option value="'.$textes['id_entite'].'">'.$textes['titre'].' - '.$textes['lang'].' - '.$textes['auteur'].'</option>');
    }
                ?>
            </select>
            <hr>
            <input type="text" name="titre[]" placeholder="titre" value="<?=$_POST['titre'][0]?>">
            <input type="text" name="auteur[]" placeholder="auteur" value="<?=$_POST['auteur'][0]?>">
            <input type="text" name="lang[]" placeholder="grc_GR" value="<?=$_POST['lang'][0]?>">
            <select name="type[]">
                <option value="poem">Vers</option>
                <option value="prose">Prose</option>
            </select><br>
            <textarea name="text[]" id="" cols="150" rows="10" placeholder="Texte grecque">
<?php
if(isset($_POST['text'][0]) and $_POST['text'][0] != ""){
echo($_POST['text'][0]);
}
else{
echo('νέοις ἀνάπτων καρδίας σοφὴν ζέσιν,
ἀρχὴν Ἔρωτα τῶν λόγων ποιήσομαι:
πυρσὸν γὰρ οὗτος ἐξανάπτει τοῖς νέοις.');
}
?></textarea>
                <hr>
                <input type="text" name="titre[]" placeholder="titre" value="<?=$_POST['titre'][1]?>">
                <input type="text" name="auteur[]" placeholder="auteur" value="<?=$_POST['auteur'][1]?>">
                <input type="text" name="lang[]" placeholder="fr_FR" value="<?=$_POST['lang'][1]?>">
                <select name="type[]">
                    <option value="poem">Vers</option>
                    <option value="prose">Prose</option>
                </select><br>
                <textarea name="text[]" id="" cols="150" rows="10" placeholder="Texte français">
<?php
if(isset($_POST['text'][1]) and $_POST['text'][1] != ""){
echo($_POST['text'][1]);
}
else{
echo('Enflammant le cœur des jeunes avec une ferveur savante,
je ferai que l’Amour soit le commencement de ces discours;
c’est lui, en effet, qui allume le flambeau pour les jeunes');
}
?>
</textarea>
                <hr class="insertAfter">
                <p id="addTranslation">Ajouter une autre langue</p>
                <input type="submit">
                </form>
            <script>
                $(document).ready(function(){
                    $("#addTranslation").on("click",function(){
                        $('<input type="text" name="titre[]" placeholder="titre"><input type="text" name="auteur[]" placeholder="auteur"><input type="text" name="lang[]" placeholder="grc_GR"><select name="type[]">                <option value="poem">Vers</option><option value="prose">Prose</option></select><br><textarea name="text[]" id="" cols="150" rows="10" placeholder="Texte grecque"></textarea><hr class="insertAfter">').insertAfter( ".insertAfter");
                        $(".insertAfter").first().removeClass("insertAfter");
                    });
                });
            </script>
            <?php
}
if($_POST['action'] == "traduction"){
    if($_POST[entite] > 0){
        $lastId = $_POST['entite'];
    }
    else{
        $newEntite = $bdd->query("INSERT INTO `naus`.`entite` (`id_entite`) VALUES (NULL);");
        $lastId = $bdd->lastInsertId();
    }
    ob_start();
    echo('[');
    $textNbr = 0;
    foreach($_POST['text'] as $text){
        $newText = $bdd->prepare("INSERT INTO `naus`.`texte` (`id_entite`, `id_texte`, `titre`, `auteur`, `texte`, `lang`, `type`) VALUES (:entite, NULL, :titre, :auteur, :texte, :lang, :type);");
        $newText->bindParam(":entite",$lastId);
        $newText->bindParam(":titre",$_POST['titre'][$textNbr]);
        $newText->bindParam(":auteur",$_POST['auteur'][$textNbr]);
        $newText->bindParam(":texte",$_POST['text'][$textNbr]);
        $newText->bindParam(":lang",$_POST['lang'][$textNbr]);
        $newText->bindParam(":type",$_POST['type'][$textNbr]);
        $newText->execute() or die('Erreur dans l\'ajout des textes');
        if($textNbr > 0){
            echo(',');
        }
        echo('{"type":"'.$_POST['type'][$textNbr].'","titre":"'.$_POST['titre'][$textNbr].'","auteur":"'.$_POST['auteur'][$textNbr].'","lang":"'.$_POST['lang'][$textNbr].'","text":[[');
        stropheJSON(nl2br($text),$textNbr);
        echo(']]}');
        $textNbr++;
    }
    echo(']');
    $json = ob_get_clean();
    $newAligne = $bdd->prepare("INSERT INTO `naus`.`alignee` (`id_alignee`, `id_entite`, `from`, `to`, `json`) VALUES (NULL, :entite, :from, :to, :json);");
    $newAligne->bindParam(":entite",$lastId);
    $newAligne->bindParam(":from",$_POST['lang'][0]);
    $newAligne->bindParam(":to",$_POST['lang'][1]);
    $newAligne->bindParam(":json",$json);
    $newAligne->execute() or die('Erreur insertion JSON aligne');
    $alignId = $bdd->lastInsertId();
    echo('<a href="align.php?alignee='.$alignId.'">commencer la traduction alignée!</a>');
}

function stropheJSON($strophe,$textNbr){
    $i = 0;
    $w = 0;
    $parts = explode('<br />', $strophe);
    foreach($parts as $value){
        if($i > 0){
            echo(',');
        }
        $i++;
        $w = versJSON($value,$w,$textNbr);
    }
}


function versJSON($vers,$word,$textNbr){
    $i = 0;
    $string = preg_match_all("/\p{Greek}+|\w+|\p{P}+/u",$vers,$matches);
    echo('[');
    $textDecal = "";
    while($textNbr > 0){
        $textDecal .= "[],";
        $textNbr--;
    }
    foreach($matches[0] as $value){
        if($i > 0){
            echo(',');
        }
        $i++;
        $word++;
        if(preg_match("/\w+/", $value)){
            echo('{"t":"'.$value.'","h":['.$textDecal.'['.$word.']]}');
        }
        elseif(preg_match("/\p{Greek}+/u", $value)){
            echo('{"t":"'.$value.'","h":['.$textDecal.'['.$word.']]}');
        }
        else{
            echo('{"p":"'.$value.'"}');
        }
    }
    echo(']');
    return $word;
}
            ?>
            </body>
        </html>

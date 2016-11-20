<?php
$configPassword = "dNh9h{E(\Qm5tB6>";
include('include/config.ini.php');
$getJSON = $bdd->prepare("UPDATE `alignee` SET json = :json where id_alignee = :alignee");
$getJSON->bindParam(":alignee",$_POST[alignee]);
$getJSON->bindParam(":json",$_POST[json]);
$getJSON->execute() or die('erreur update JSON');
$JSON = $getJSON->fetch();
echo('Bimbamboom JSON updated');
?>

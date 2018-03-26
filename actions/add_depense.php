<?php

require_once("../include/connect_bd.inc.php");
require_once("../include/redirection.inc.php");
require_once("../include/constants.inc.php");
require_once("../include/compte.inc.php");


$payeur      = $_POST["payeur"];
$intitule    = $_POST["intitule"];
$date        = $_POST["annee"]."/".$_POST["mois"]."/".$_POST["jour"];
$prix        = $_POST["intitule"] == MONTANT_FICTIF ? $_POST["prix"]*2 : $_POST["prix"];

$form_values = array("payeur"=>$payeur, "intitule"=>$intitule, "date"=>$date, "prix"=>$prix);

connect_bd();
add_depense($form_values);

$params = [];
if($_GET["mois"]) {
  $params[] = "mois=".$_GET["mois"];
}
if($_GET["annee"]) {
  $params[] = "annee=".$_GET["annee"];
}

redirection("../index.php?" . join("&", $params));

?>

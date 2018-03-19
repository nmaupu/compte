<?php

require_once("../include/connect_bd.inc.php");
require_once("../include/redirection.inc.php");
require_once("../include/constants.inc.php");


$intitule = $_POST["intitule"];

$query       = "INSERT INTO `".TBL_PREFIX."intitule` ( `id_intitule` , `nom_intitule`) ".
               "VALUES ('', '$intitule')";
connect_bd();
mysql_query($query);

redirection("../index.php");

?>
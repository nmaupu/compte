<?php

require_once("include/connect_bd.inc.php");
require_once("include/constants.inc.php");
require_once("include/compte.inc.php");

session_start();
if(isset($_GET["logout"]))
{
	unset($_SESSION["login"]);
	unset($_SESSION["perm_skip"]);
}

if($_POST["login"])
{
	$_SESSION["login"] = $_POST["login"];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<link rel="stylesheet" type="text/css" href="css/screen.css" media="screen" title="Normal" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" charset="UTF-8"/>
	<title>Dépenses communes</title>
</head>
<body>

<?php
if(!$_SESSION["login"])
{
	ob_start();
?>
<form class="login" action="index.php" method="post">
<fieldset>
<legend>C'est qui ?</legend>
<input type="radio" name="login" value="Nicolas" checked="checked" /> Nicolas
<input type="radio" name="login" value="Marianne" /> Marianne
<input class="button" type="submit" value="OK" />
</fieldset>
</form>
<?php
}
else
{
  $bd = connect_bd();

  $permanent = get_permanent_not_threated();

  // on saute le traitement des entrees permanentes si besoin
  $print_main_page = $_POST["perm_skip"];
  $_SESSION["perm_skip"] = $_SESSION["perm_skip"] ? $_SESSION["perm_skip"] : $print_main_page;

  if ( !$_SESSION["perm_skip"] )
  {
    if ( $permanent && is_array($permanent) )
    {
      if( ! $_POST["confirm_perm"] )
      {
        ?>
          <form name="confirm_perm" method="post" action="">
          <input type="hidden" name="confirm_perm" value="confirm_perm" />
        <?php
          foreach ( $permanent as $key => $value )
          display_permanent($value);
        ?>
          <input type="submit" value="Confirmer" />
          </form>
	  <form method="POST" action="">
	    <input type="hidden" name="perm_skip" value="perm_skip" />
	    <input type="submit" value="Passer pour le moment" />
      	  </form>
        <?php
      } // if not confirmed
      else
      {
        // On confirme et on insere les entrees
        foreach ( $permanent as $key => $value )
        {
          $value["prix"] = $_POST["perm_".$value["id"]] ? $_POST["perm_".$value["id"]] : $value["montant"];
          update_permanent($value);
        }
  
        $print_main_page = TRUE;
      }
    }
  }
  
  if ( $_SESSION["perm_skip"] || ( !$permanent && ! is_array($permanent)) )
  {
?>

<div class="contenu">
<h1>Dépenses communes</h1>


<?php

/* Génération de la date courante */
$date     = date("j-m-Y");
$tab_date = explode("-", $date);
$jour     = ltrim($tab_date[0], "0");
$mois     = $_GET["mois"] ? ltrim($_GET["mois"], "0") : ltrim($tab_date[1], "0");
$annee    = $_GET["annee"] ? ltrim($_GET["annee"], "0") : ltrim($tab_date[2], "0");

/* On rajoute un 0 de prefixe si besoin */
$mois = $mois<10 ? '0'.$mois : $mois;
$jour = $jour<10 ? '0'.$jour : $jour;

?>

<!-- Creation des comboboxes permettant de choisir le mois et l'année -->
<div><a href="permanent.php">Ajouter une dépense récurrente</a></div>
<form class="date" action="" method="get">
	<fieldset>
	<legend>Date</legend>
	<select name="mois" id="mois">
	<?php
		for($i=1; $i<=12; $i++)
		{
			$selected = $i==$mois ? ' selected="selected"' : '';
			$i = $i<10 ? '0'.$i : $i;
			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
	?>
	</select>
	<select name="annee" id="annee">
	<?php 
		for($i=$tab_date[2]-2; $i<=$tab_date[2]+2; $i++)
		{
			$selected = $i == $annee ? ' selected="selected"' : '';
			echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
	?>
	</select>
	<input type="submit" value="OK" />
	</fieldset>
</form>

<?php


/* Résultats des requêtes */
$res_nico     = get_ecriture("Nicolas", $mois, $annee);
$res_marianne = get_ecriture("Marianne", $mois, $annee);
$res_remb     = get_remboursement($mois, $annee);

/* Tableau des requêtes */
$tab_marianne         = get_table_depense($res_marianne);
$tab_nico             = get_table_depense($res_nico);
$tab_remb["Nicolas"]  = get_table_remboursement($res_remb, "Nicolas");
$nb_sql = $res_remb && mysql_num_rows($res_remb) > 0 ? mysql_num_rows($res_remb) : 0;
if($nb_sql > 0)
	mysql_data_seek($res_remb, 0);
$tab_remb["Marianne"] = get_table_remboursement($res_remb, "Marianne");
if($nb_sql > 0)
	mysql_data_seek($res_remb, 0);

$gd_total_nico     = $tab_nico["prix_total"]+2*$tab_remb["Nicolas"]["prix_total"];
$gd_total_marianne = $tab_marianne["prix_total"]+2*$tab_remb["Marianne"]["prix_total"];

$tot_diff = abs($gd_total_nico-$gd_total_marianne);
$tot_remb = round($tot_diff/2, 2);

$tot_diff = round($tot_diff, 2);
$tot_remb = round($tot_remb, 2);

if($tot_diff < 1)
	$tot_diff = $tot_remb = 0;



$chef_du_mois   = $gd_total_nico > $gd_total_marianne ? "Nicolas" : "Marianne";
if($tot_remb != 0)
{
	$class_marianne = $chef_du_mois == "Nicolas" ? "warning" : "ok";
	$class_nico     = $chef_du_mois == "Marianne" ? "warning" : "ok";
}
else
{
	$class_marianne = "ok";
	$class_nico     = "ok";
}
$class_remb     = "ok";


echo '<p>';
print_table_depense("Marianne", $tab_marianne, $class_marianne);
print_table_depense("Nicolas", $tab_nico, $class_nico);
if($tab_remb["Nicolas"]["prix_total"] > 0 || $tab_remb["Marianne"]["prix_total"] > 0)
	print_table_remboursement("Remboursements", $tab_remb, $class_remb);
echo '</p>';


/* Chargement des formulaires */
$form_ajout_depense       = get_form_ajout_depense($_SESSION["login"], $jour, $mois, $annee);
$payeur = $chef_du_mois == "Nicolas" ? "Marianne" : "Nicolas";
$form_ajout_remboursement = get_form_ajout_remboursement($payeur, $jour, $mois, $annee, $tot_remb);
$form_ajout_intitule      = get_form_ajout_intitule();
$form_divers              = get_form_divers();

?>

</div>

<div class="recap">
	<p>Différence de <strong><?php echo $tot_diff; ?></strong> &euro;<br />
	Reste à rembourser : <strong><?php echo $tot_remb; ?></strong> &euro;</p>
</div>

<div class="telecommande">
<?php
echo $form_ajout_depense; 
echo $form_ajout_remboursement;
echo $form_ajout_intitule;
echo $form_divers;
?>

</div>

<?php
  } //if permanent
} //if(!$_SESSION["login"])
?>

</body>
</html>

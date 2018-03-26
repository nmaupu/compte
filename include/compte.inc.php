<?php
function get_table_depense($sql_res)
{
	$nb     = mysql_num_rows($sql_res);
	$payeur = "";
	if($nb == 0)
	{
		return array();
	}
	else
	{
		$ret = array();
		$tot = 0;
		for($i=0; $i<$nb; $i++)
		{
			$row = mysql_fetch_array($sql_res);
			$ret["result"][$i] = array("date"=>$row["date"], "intitule"=>$row["intitule"], "prix"=>$row["prix"]);
			$tot += $row["prix"];
			if(!$payeur)
				$payeur = $row["payeur"];
		}
	}
	
	$ret["prix_total"] = $tot;
	$ret["payeur"]     = $payeur;
	return $ret;
}

function get_table_remboursement($sql_res, $pay)
{
	$nb     = mysql_num_rows($sql_res);
	$payeur = "";
	if($nb == 0)
	{
		return array();
	}
	else
	{
		$ret  = array();
		$tot  = 0;
		$cont = 0;
		for($i=0; $i<$nb; $i++)
		{
			$row	 = mysql_fetch_array($sql_res);
			if($row["payeur"] == $pay)
			{
				$ret["result"][$cont++] = array("date"=>$row["date"], "prix"=>$row["prix"]);
				$tot    += $row["prix"];
			}
		}
	}
	
	$ret["prix_total"] = $tot;
	return $ret;
}

function print_table_depense($name, $tab_dep, $class_total)
{
	$c = 3;
?>

<table>
	<tr>
		<th colspan=<?php echo '"'.$c.'">'.$name; ?></th>
	</tr>
	<?php
		$nb = count($tab_dep["result"]);
		if($nb == 0)
		{
			echo "\t<tr>\n";
			echo "\t\t".'<td colspan="'.$c.'">Aucune dépense</td>'."\n";
			echo "\t".'</tr>'."\n";
		}
		else
		{
			echo "\t<tr>\n";
			echo "\t\t".'<td class="namecol_date">Date</td>'."\n";
			echo "\t\t".'<td class="namecol_intitule">Intitulé</td>'."\n";
			echo "\t\t".'<td class="namecol_prix">Prix</td>'."\n";
			echo "\t".'</tr>'."\n";
			for($i=0; $i<$nb; $i++)
			{
				$class = $i%2==0 ? "pair" : "impair";
				echo "\t".'<tr class="'.$class.'">'."\n";
				echo "\t\t".'<td>'.formate_date_sql($tab_dep["result"][$i]["date"], "/").'</td>'."\n";
				echo "\t\t".'<td>'.$tab_dep["result"][$i]["intitule"].'</td>'."\n";
				echo "\t\t".'<td class="nombre">'.$tab_dep["result"][$i]["prix"].'</td>'."\n";
				echo "\t".'</tr>'."\n";
			}
			
			echo "\t".'<tr class="'.$class_total.'">'."\n\t\t".'<td colspan="'.($c-1).'" class="total">Total</td>'."\n\t\t".'<td class="nombre">'.$tab_dep["prix_total"].'</td>'."\n\t".'</tr>'."\n";
		}
	?>
</table>
<?php
	return $prix_tot;
}


function print_table_remboursement($name, $tab_remb, $class_total)
{
	$c = 3;
?>

<table>
	<tr>
		<th colspan=<?php echo '"'.$c.'">'.$name; ?></th>
	</tr>
	<?php
		echo "\t<tr>\n";
		echo "\t\t".'<td class="namecol_date">Nom</td>'."\n";
		echo "\t\t".'<td class="namecol_intitule">Date</td>'."\n";
		echo "\t\t".'<td class="namecol_prix">Montant</td>'."\n";
		echo "\t".'</tr>'."\n";
		
		$cont  = 0;
		$nb = count($tab_remb["Nicolas"]["result"]);
		for($i=0; $i<$nb; $i++)
		{
			$class = $cont%2==0 ? "pair" : "impair";
			$cont++;
			echo "\t".'<tr class="'.$class.'">'."\n";
			echo "\t\t".'<td>Nicolas</td>'."\n";
			echo "\t\t".'<td>'.formate_date_sql($tab_remb["Nicolas"]["result"][$i]["date"], "/").'</td>'."\n";
			echo "\t\t".'<td class="nombre">'.$tab_remb["Nicolas"]["result"][$i]["prix"].'</td>'."\n";
			echo "\t".'</tr>'."\n";
		}
		
		$nb = count($tab_remb["Marianne"]["result"]);
		for($i=0; $i<$nb; $i++)
		{
			$class = $cont%2==0 ? "pair" : "impair";
			$cont++;
			echo "\t".'<tr class="'.$class.'">'."\n";
			echo "\t\t".'<td>Marianne</td>'."\n";
			echo "\t\t".'<td>'.formate_date_sql($tab_remb["Marianne"]["result"][$i]["date"], "/").'</td>'."\n";
			echo "\t\t".'<td class="nombre">'.$tab_remb["Marianne"]["result"][$i]["prix"].'</td>'."\n";
			echo "\t".'</tr>'."\n";
		}
		
		echo "\t".'<tr class="'.$class_total.'">'."\n\t\t".'<td colspan="'.($c-1).'" class="total">Total Nicolas</td>'."\n\t\t".'<td class="nombre">'.$tab_remb["Nicolas"]["prix_total"].'</td>'."\n\t".'</tr>'."\n";
		echo "\t".'<tr class="'.$class_total.'">'."\n\t\t".'<td colspan="'.($c-1).'" class="total">Total Marianne</td>'."\n\t\t".'<td class="nombre">'.$tab_remb["Marianne"]["prix_total"].'</td>'."\n\t".'</tr>'."\n";
	?>
</table>
<?php
	return $prix_tot;
}

function formate_date_sql($date, $sep)
{
	$tab = explode("-", $date);
	return $tab[2].$sep.$tab[1].$sep.$tab[0];
}

function print_combo_intitules()
{
	$query = "SELECT * FROM ".TBL_PREFIX."intitule ORDER BY nom_intitule";
	$res   = mysql_query($query);
	$nb    = mysql_num_rows($res);
	
	echo '<select id="intitule" name="intitule">';
			
	for($i=0; $i<$nb; $i++)
	{
		$row = mysql_fetch_array($res);
		echo '<option value="'.$row["id_intitule"].'">'.$row["nom_intitule"].'</option>';
	}
	
	echo '</select>';
}

function get_ecriture($payeur, $mois, $annee)
{
	$query = "SELECT date_dep as date, payeur_dep as payeur, nom_intitule as intitule, prix_dep as prix FROM ".TBL_PREFIX."dep_commune, ".TBL_PREFIX."intitule ".
	         "WHERE payeur_dep='$payeur' ".
	         "AND intitule_dep=id_intitule ".
	         "AND date_dep LIKE '".$annee."-".$mois."-%' ".
	         "ORDER BY date";
	return mysql_query($query);
}

function get_remboursement($mois, $annee)
{
	$query = "SELECT date_dep as date, payeur_dep as payeur, prix_dep as prix FROM ".TBL_PREFIX."dep_commune ".
	         "WHERE date_dep LIKE '".$annee."-".$mois."-%' ".
	         "AND intitule_dep = '-1'".
	         "ORDER BY date";
	return mysql_query($query);
}

function get_form_ajout_depense($payeur, $jour, $mois, $annee)
{
	ob_start();
	$checked_nico     = "";
	$checked_marianne = "";
	if($payeur == "Nicolas")
		$checked_nico = 'checked="checked"';
	else
		$checked_marianne = 'checked="checked"';
	if($_GET["mois"]) {
		$params[] = "mois=".$_GET["mois"];
	}
	if($_GET["annee"]) {
		$params[] = "annee=".$_GET["annee"];
	}
?>
<!-- Formulaire d'ajout d'une depense commune -->
<form class="ajout_dep" action="actions/add_depense.php?<?php echo join("&", $params); ?>" method="post">
<fieldset>
	<legend>Ajouter une dépense commune</legend>
	<div class="row"><label for="payeur">Payeur</label><input type="radio" name="payeur" value="Nicolas" <?php echo $checked_nico; ?>/>Nicolas<input type="radio" name="payeur" value="Marianne" <?php echo $checked_marianne; ?>/>Marianne</div>
	<div class="row"><label for="intitule">Intitulé</label><?php print_combo_intitules(); ?></div>
	<div class="row"><label for="date">Date</label><span name="date" id="date"><input class="date" type="text" name="jour" id="jour" value="<?php echo $jour; ?>" />&nbsp;/&nbsp;<input class="date" type="mois" name="mois" id="mois" value="<?php echo $mois; ?>" />&nbsp;/&nbsp;<input class="date" type="text" name="annee" id="annee" value="<?php echo $annee; ?>"/></span></div>
	<div class="row"><label for="prix">Prix</label><input class="prix" type="text" name="prix" id="prix" /> &euro;</div>
	<div class="row"><input class="button" type="submit" value="Ajouter" /></div>
</fieldset>
</form>
<?php
	$form = ob_get_contents();
	ob_end_clean();
	
	return $form;
}

function get_form_ajout_remboursement($default_payeur, $default_jour, $default_mois, $default_annee, $default_prix)
{
	ob_start();
?>
<!-- Formulaire d'ajout d'un remboursement -->
<form class="ajout_remb" action="actions/add_depense.php" method="post">
<fieldset>
	<legend>Ajouter un remboursement</legend>
	<input type="hidden" name="intitule" id="intitule" value="-1" />
	<?php
		$checked_nico     = "";
		$checked_marianne = "";
		if($default_payeur == "Nicolas")
			$checked_nico = 'checked="checked"';
		else
			$checked_marianne = 'checked="checked"';
	?>
	<div class="row"><label for="payeur">Payeur</label><input type="radio" name="payeur" value="Nicolas" <?php echo $checked_nico; ?>/>Nicolas<input type="radio" name="payeur" value="Marianne" <?php echo $checked_marianne; ?>/>Marianne</div>
	<div class="row"><label for="date">Date</label><span name="date" id="date"><input class="date" type="text" name="jour" id="jour" value="<?php echo $default_jour; ?>" />&nbsp;/&nbsp;<input class="date" type="mois" name="mois" id="mois" value="<?php echo $default_mois; ?>" />&nbsp;/&nbsp;<input class="date" type="text" name="annee" id="annee" value="<?php echo $default_annee; ?>" /></span></div>
	<div class="row"><label for="prix">Prix</label><input class="prix" type="text" name="prix" id="prix" value="<?php echo $default_prix; ?>" /> &euro;</div>
	<div class="row"><input class="button" type="submit" value="Ajouter" /></div>
</fieldset>
</form>
<?php
	$form = ob_get_contents();
	ob_end_clean();
	
	return $form;
}

function get_form_ajout_intitule()
{
	ob_start();
?>
<!-- Formulaire d'ajout d'un intitule -->
<form class="ajout_intitule" action="actions/add_intitule.php" method="post">
<fieldset>
	<legend>Ajouter un intitulé</legend>
	<div class="row"><label for="intitule">Nom de l'intitulé</label><input type="text" name="intitule" /></div>
	<div class="row"><input class="button" type="submit" value="Ajouter" /></div>
</fieldset>
</form>
<?php
	$form = ob_get_contents();
	ob_end_clean();
	
	return $form;
}

function get_form_divers()
{
	ob_start();
?>
<form class="divers" action="" method="post">
<fieldset>
	<legend>Divers</legend>
	<div class="row"><a href="?logout">Quitter</a></div>
</fieldset>
</form>
<?php
	$form = ob_get_contents();
	ob_end_clean();
	
	return $form;
}

function get_permanent_not_threated()
{
  $query = 
    "SELECT * FROM ".TBL_PREFIX."dep_permanent ".
    "WHERE last_updated < IF(".
    "  DATE_FORMAT(NOW(), '%d') < jour, ".
    "  CONCAT(DATE_FORMAT(NOW(), '%Y-'), DATE_FORMAT(NOW(), '%m') - 1, '-', jour), ".
    "  CONCAT(DATE_FORMAT(NOW(), '%Y-'), DATE_FORMAT(NOW(), '%m'), '-', jour) ".
    ") ";

  //echo $query;
  
  $res = mysql_query($query);

  while ( ($p = mysql_fetch_array($res)) != NULL)
    $ret[] = $p;

  return $ret;
}

function update_permanent($vals)
{
  $date  = date("Y-m-d", mktime());
  $tab   = explode("-", $date);
  $annee = $tab[0];
  $mois  = $tab[1];
  $jour  = $tab[2];

  $vals["date"] = "$annee-$mois-".$vals["jour"];
  
  add_depense($vals);
  $query = "UPDATE ".TBL_PREFIX."dep_permanent SET last_updated=NOW() WHERE id=".$vals["id"];
  mysql_query($query);
}

function display_permanent($vals)
{
  $date = $vals["last_updated"] != "0000-00-00" ? formate_date_sql($vals["last_updated"], "/") : "N/A";
  echo "<div>".$date." - ".$vals["payeur"]." - ".$vals["intitule"]." - ".$vals["date"].'<input type="text" name="perm_'.$vals["id"].'" value="'.$vals["montant"].'" />&nbsp;&euro;</div>';
}

function add_depense($form_values)
{
  $date     = $form_values["date"];
  $intitule = $form_values["intitule"];
  $prix     = $form_values["prix"];
  $payeur   = $form_values["payeur"];
  
  $query = 
    "INSERT INTO ".TBL_PREFIX."dep_commune ".
    "( id_dep , date_dep , intitule_dep , prix_dep, payeur_dep ) ". 
    "VALUES ('', " . ($date ? "'$date'" : "NOW()" ) . ", '$intitule', '$prix', '$payeur')";
  mysql_query($query);
}

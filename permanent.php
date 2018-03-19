<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
        <link rel="stylesheet" type="text/css" href="css/screen.css" media="screen" title="Normal" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Ajout d'une entrée permanente</title>
</head>
<body>
<?php
  require_once("include/constants.inc.php");
  require_once("include/connect_bd.inc.php");
  connect_bd();
  require_once("include/compte.inc.php");


  $client=$_SERVER['REMOTE_ADDR'];
  if ($client != "127.0.0.1" && $client != "localhost")
  {
  //  die ("Accès à cette page non authorisé");
  }

  // formulaire
  if ($_POST["validated"])
  {
    $form_values = array("payeur"   => $_POST["payeur"], 
      "montant"  => $_POST["montant"], 
      "intitule" => $_POST["intitule"], 
      "jour"     => $_POST["jour"],
    );
    $error = validate_permanent($form_values);

    if( !$error || count($error) == 0 )
    {
      insert_permanent($form_values);
      echo '<div class="ok">Ajouté avec succès</div>';
    }
    else if ( is_array($error) )
    {
      foreach ($error as $key=>$value)
        echo "$value<br />";
      echo "<hr />";
    }
  }

  // Ajout d'une entrée permanente
?>
  <form action="" method="post">
    <input type="hidden" name="validated" value="validated" />
    <label for="payeur">Payeur&nbsp;:&nbsp;</label>
         <input type="radio" name="payeur" value="nicolas" />Nicolas
	 <input type="radio" name="payeur" value="marianne" />Marianne<br />
    <label for="jour">Jour de récurrence&nbsp;:&nbsp;</label>
         <input name="jour" type="text" value="<?php echo date("d", mktime()); ?>" /><br />
    <label for="intitule">Intitulé&nbsp;:&nbsp;</label>
         <?php print_combo_intitules(); ?><br />
    <label for="montant">Montant&nbsp;:&nbsp;</label>
         <input type="text" name="montant" /><br />
    <input type="submit" value="Valider" />
  </form>
</body>

<?php
  function validate_permanent(&$form_values)
  {
    if ( !in_array($form_values["payeur"], array("nicolas", "marianne")) )
      $error[] = "Le payeur doit être Nicolas ou Marianne";

    if ( $form_values["jour"] < 1 || $form_values["jour"] > 28 )
      $error[] = "Le jour doit être compris entre 1 et 28";

    $form_values["montant"] = (int)str_replace(',', '.', $form_values["montant"]);

    if ( !is_numeric($form_values["montant"]) )
      $error[] = "Le montant doit être numérique";
    
    return $error;
  }

  function insert_permanent($form_values)
  {
    $payeur   = $form_values["payeur"];
    $montant  = $form_values["montant"];
    $intitule = $form_values["intitule"];
    $jour     = $form_values["jour"];
    
    $query = "INSERT INTO dep_permanent (id, payeur, montant, intitule, jour) VALUES ('', '$payeur', '$montant', '$intitule', '$jour')";
    mysql_query ($query);
  }
?>

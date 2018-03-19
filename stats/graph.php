<?php
  require_once("../include/connect_bd.inc.php");
  require_once("../include/constants.inc.php");
  require_once("../include/compte.inc.php");
  connect_bd();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <link rel="stylesheet" type="text/css" href="/css/screen.css" media="screen" title="Normal" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Compte - Module de statistiques</title>
  </head>
<body>
  <div class="telecommande">
    <form class="misc" method="get" action="/stats/graph.php">
    <?php
      echo get_inputhidden_from_getparam(array("startyear", "startmonth", "endyear", "endmonth"));
    ?>
    <fieldset>
      <legend>Filtre d'affichage</legend>
      <div class="row">
        <label for="startyear">Date de d&eacute;part</label>
        <?php echo get_date_input("startyear", "startmonth", $_GET["startyear"], $_GET["startmonth"]) ?>
      </div>
      <div class="row">
        <label for="endyear">Date de fin</label>
        <?php echo get_date_input("endyear", "endmonth", $_GET["endyear"], $_GET["endmonth"], true) ?>
      </div>
    </fieldset>
    <div class="row">
      <input class="button" type="submit" value="Filtrer"/>
    </div>
  </form>
  </div>
  <div class="telecommande" style="position:absolute; width:300px; top:140px;">
  <form class="misc" method="get" action="/stats/graph.php">
    <?php
      echo get_inputhidden_from_getparam(array("filter", "intitule"));
    ?>
    <input type="hidden" name="filter" value="<?php
        $set_intitules="";
        if($_GET["filter"] && $_GET["intitule"]) {
	  $set_intitules = $_GET["filter"]."|".$_GET["intitule"];
	} else if($_GET["intitule"]) {
	  $set_intitules = $_GET["intitule"];
	}
	$array_set_intitules = explode("|", $set_intitules);
	$array_set_intitules = array_unique($array_set_intitules);
	$set_intitules = implode("|", $array_set_intitules);
	echo $set_intitules;
      ?>" />
    <fieldset>
      <legend>Filtre intitul&eacute;</legend>
      <div class="row">
        <label for="intitule">Intitul&eacute;</label>
        <?php print_combo_intitules(); ?>
      </div>
      <div class="row">
       <input class="button" type="submit" value="Ajouter"/> 
      </div>
    </fieldset>
  </form>
  </div>
  <div class="contenu">
    <?php
      if(isset($_GET["startyear"]) && isset($_GET["startmonth"]) && 
         isset($_GET["endyear"]) && isset($_GET["endmonth"])) {
	   $startdate = $_GET["startyear"]."-".$_GET["startmonth"]."-01";
	   $enddate    = $_GET["endyear"]."-".$_GET["endmonth"]."-31";
	   echo '<img src="/stats/dep_by_date.php?start='.$startdate.'&end='.$enddate.'&filter='.$_GET["filter"].'&intitule='.$_GET["intitule"].'" alt="GraphArtichow" />';
      }
    ?>
    <img src="/stats/dep_by_date.php?start=-1&end=-1&filter=<?php echo $_GET["filter"] ?>&intitule=<?php echo $_GET["intitule"] ?>" alt="GraphArtichow" />
  </div>
</body>
</html>
<?php


/*****************************************/

function get_date_input($fieldname_year, $fieldname_month, $curyear=0, $curmonth=0, $current_date_sel=false) {
  ob_start();
?>
  <select name="<?php echo $fieldname_month  ?>">
<?php 
  $currentdate = getdate();
  for($cpt=1; $cpt<=12; $cpt++) {
    $selected = ($current_date_sel && 
                 $curmonth <= 0 && 
                 $cpt == $currentdate["mon"]
                ) || $cpt == $curmonth ? 'selected="selected"' : "";
    echo '<option value="'.$cpt.'" '.$selected.'>'.$cpt.'</option>';
  }
?>
    </select>
    <select name="<?php echo $fieldname_year ?>">
<?php
  for($year=$currentdate["year"]; $year>=$currentdate["year"]-10; $year--) {
    $selected = ($current_date_sel &&  
                 $curyear <= 0 &&  
                 $year == $currentdate["mon"]
                ) || $year == $curyear ? 'selected="selected"' : "";
    echo '<option value="'.$year.'" '.$selected.'>'.$year.'</option>';
  }
?>
  </select>
<?php
  return ob_get_clean();
}

function get_getparam() {
  $paramurl="?a";

  foreach($_GET as $key => $val) {
    $paramurl .= "&$key=$val";
  }

  return $paramurl;
}

function replace_getparam($param, $newval) {
  $paramurl="?a";

  foreach($_GET as $key => $val) {
    $paramurl .= $key != $param ? "&$key=$val" : "";
  }

  $paramurl .= "&$key=$newval";
  return $paramurl;
}

function add_filter_intitule_to_getparam($intitules) {
  $filter = $_GET["filter"] ? $_GET["filter"] : "";
  $current_intitules = explode("|,", $filter);

  $merge_intitule = array_merge($current_intitules, $intitules);
  return implode("|", array_unique($merge_intitule, SORT_NUMERIC));
}

function get_inputhidden_from_getparam($skip_keys=array()) {
  ob_start();

  foreach($_GET as $key => $val) {
    if(!in_array($key, $skip_keys))
      echo '<input type="hidden" name="'.$key.'" value="'.$val.'" />';
  }

  return ob_get_clean();
}
?>

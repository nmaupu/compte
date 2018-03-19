<?php
require_once("../include/libgraph.php");
require_once("../include/connect_bd.inc.php");

$bd = connect_bd();
$curdate = getdate();

$start    = $_GET["start"] == -1 ? "1970-01-01" : $_GET["start"];
$end      = $_GET["end"] == -1 ? $curdate["year"]."-".$curdate["mon"]."-".$curdate["mday"] : $_GET["end"];
$filters  = isset($_GET["filter"]) ? explode("|", $_GET["filter"]) : "";
$intitule = isset($_GET["intitule"]) ? $_GET["intitule"] : "";

$query_filters = $intitule ? array($intitule) : array();
if($filters) {
  foreach($filters as $key=>$val) {
    if(is_numeric($val))
      $query_filters[] = $val;
  }
}

$string_filter = count($query_filters) > 0 ? "intitule_dep IN (".implode(",", $query_filters).")" : "1";
$title = "Dépense de $start à $end";

$query = "select sum(prix_dep) as total, 
  DATE_FORMAT(date_dep, '%M %Y') as date 
  from dep_commune 
  where intitule_dep > 0
  AND   date_dep >= '$start'
  AND   date_dep <= '$end'
  AND   $string_filter
  GROUP BY date ORDER BY DATE_FORMAT(date_dep, '%Y-%m')";

//print_r($query);
$res = mysql_query($query);
$nb = mysql_num_rows($res);

$dates=array();
$totals=array();
for ($i=0; $i<$nb; $i++)
{   
   $row = mysql_fetch_array($res);
   $dates[] = $row["date"];
   $totals[] = $row["total"];
}   

if($nb > 0) {
  $graph_data = new GraphData($dates, $totals);
  $graph = getgraph_histo_mean($graph_data, $title);
} else {
  $graph = new Graph(150, 70);
  $label = new Label("Rien à afficher");
  $label->setFont(new Tuffy(9));
  $graph->addLabel($label, .50, .50);
}

$graph->draw();
?>

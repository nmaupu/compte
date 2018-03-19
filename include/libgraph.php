<?php
require_once("../include/connect_bd.inc.php");
require_once "../include/artichow/BarPlot.class.php";
require_once "../include/artichow/LinePlot.class.php";

class GraphData {
  private $abs = array();
  private $values = array();
  private $avg = array();
  private $valAvg = 0;
  
  function __construct($abs, $values) {
    $this->abs = $abs;
    $this->values = $values;

    // Calcul des valeurs mediannes
    $nb = count($values);
    $sumtotal = 0;
    $i = 1;
    $avg = array();
    foreach ($values as $key => $val) {
      $sumtotal += $val;
      $avg[] = $sumtotal / ($i++);
    }

    $this->avg = $avg;
    $this->valAvg = round (($sumtotal / $i), 2);
  }

  function getAbs() {
    return $this->abs;
  }

  function getValues() {
    return $this->values;
  }

  function getAvg() {
    return $this->avg;
  }

  function getValAvg() {
    return $this->valAvg;
  }

  function toString() {
    print_r($this->abs);
    print_r($this->values);
    print_r($this->avg);
    print_r($this->valAvg);
  }

  function __destruct() {
  }
}

function getgraph_histo_mean($graph_data, $title="", $pad_L=40, $pad_R=10, $pad_H=30, $pad_B=100) {
  // colors
  $blue = new Color(150, 150, 230, 50);

  // Il est toujours nécessaire de donner une taille à la création de votre graphique.
  $graph = new Graph(800, 350);

  // Les valeurs à afficher sur la courbe
  $values  = $graph_data->getValues();
  $abs     = $graph_data->getAbs();
  $avg     = $graph_data->getAvg();
  $val_avg = $graph_data->getValAvg();
  // Trie les values en ordre inverse afin d'avoir le max facilement
  $values_tmp = $values;
  rsort($values_tmp);
  $val_min = $values_tmp[count($values_tmp)-1];
  $val_max = $values_tmp[0];
  $minY = 0;
  $maxY = $val_max;

  $plot1 = new BarPlot($values,1,1,0);
  $plot1->setYMin($minY);
  $plot1->setYMax($maxY);
  $plot1->setBarColor($blue);

  $plot2=new LinePlot($avg, LinePlot::MIDDLE);
  $plot2->setYMin($minY);
  $plot2->setYMax($maxY);
  $plot2->mark->setType(Mark::SQUARE);
  $plot2->mark->setSize(7);
  $plot2->mark->setFill(new White);
  $plot2->mark->border->show();

  // plots group options
  $group = new PlotGroup;
  $group->setBackgroundColor(
     new Color(240, 240, 240)
  );
  $group->axis->bottom->setLabelText($abs);
  $group->axis->bottom->label->setAngle(60);
  $group->axis->bottom->label->setAlign(Label::RIGHT, Label::BOTTOM);
  $group->axis->bottom->label->setPadding(0,-7,0,0);
  $group->axis->bottom->setPadding(20,20,20,20);
  $group->axis->left->setLabelPrecision(0);
  $group->setBackgroundGradient(
     new LinearGradient(
        // On donne deux couleurs pour le dégradé
        new Color(210, 210, 210),
        new Color(250, 250, 250),
        // On spécifie l'angle du dégradé linéaire
        // 0° pour aller du haut vers le bas
        0   
     )   
  );
  
  
  $label_legend = new Label("Min: $val_min - Max: $val_max - Avg: $val_avg");
  $label_legend->setFont(new Tuffy(9));

  // Ajout des courbes
  $group->add($plot1);
  $group->add($plot2);
  $group->setPadding($pad_L, $pad_R, $pad_H, $pad_B);

  $graph->add($group);
  $label_title=new Label($title);
  $label_title->setFont(new Tuffy(12));
  $graph->addLabel($label_title, .50, .035);
  $graph->addLabel($label_legend, .50, .96);

  return $graph;
}
?>

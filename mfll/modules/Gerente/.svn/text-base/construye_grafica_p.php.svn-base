<?php
include_once ("../$_includesdir/jpgraph/jpgraph.php");
include_once ("../$_includesdir/jpgraph/jpgraph_bar.php");
include_once ("../$_includesdir/jpgraph/jpgraph_line.php");
include_once ("../$_includesdir/jpgraph/jpgraph_error.php");

// Create the graph. These two calls are always required
$graph = new Graph(750,650,"auto");

$graph->SetScale("textlin");
//$graph->SetScale("intlin",0,0,1,$total_modelos);
$graph->SetFrame(true,'#808000',1);
$graph->SetColor("#ffffff");
$graph->img->SetMargin(40,40,60,160);

$data=array(1);
$p1 = new LinePlot($data);
$p1->SetColor("#808000");
$p1->SetLegend('Autos Comprados');
$graph->Add($p1);


$p2 = new LinePlot($data);
$p2->SetColor("#fbcd33");
$p2->SetLegend('Autos Manejados');
$graph->Add($p2);

$b1plot = new BarPlot($data1y);
$b1plot->SetFillColor("#808000");
$b1plot->SetValuePos('center');
$b1plot->value->Show();
$b1plot->value->SetFormat('%d');
$b1plot->value->SetColor('#ffffff');

$b2plot = new BarPlot($data2y);
$b2plot->SetFillColor("#fbcd33");
$b2plot->SetValuePos('center');
$b2plot->value->Show();
$b2plot->value->SetFormat('%d');
$b2plot->value->SetColor('#ffffff');


$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
$gbplot->SetWidth(0.8);

$graph->Add($gbplot);
$graph->title->SetColor("#808000");
$graph->title->Set($titulog);
$graph->xaxis->SetTickLabels($data_titulos);
$graph->xaxis->SetFont(FF_FONT1);
$graph->xaxis->SetColor("#333333");
$graph->xaxis->SetLabelAngle(90);
$graph->yaxis->SetFont(FF_FONT1);
$graph->yaxis->SetColor("#333333");
$graph->yaxis->SetLabelFormatCallback('yLabelFormat');
$_grafico = $graph->StrokeCSIM('auto','',0, true);


function yLabelFormat($aLabel) {
    return number_format($aLabel, 0, ',', ' ');
}
?>

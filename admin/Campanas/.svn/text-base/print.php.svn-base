<?
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $campana_id;
$_theme = "";
if (!$campana_id) header("location: index.php?_module=$_module");
$sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error");
global $nombre_campana;
list($nombre_campana) = $db->sql_fetchrow($result);

require("$_includesdir/fpdf.php");
define('FPDF_FONTPATH',"$_includesdir/fonts/");


class PDF extends FPDF
{
//Cabecera de página
function Header()
{
    global $nombre_campana;
    global $title;
    //Logo
    $this->Image("../img/logo.png", 10, 8, 20, 20);
    //Arial bold 15
    $this->SetFont('Arial','B',15);
    //Movernos a la derecha
    $this->Cell(25);
    //Título
    $this->Cell(0,10, "Plan de Marketing:", 0, 1, 'C');
    $this->Cell(25);
    $this->SetFont('Arial','B',18);
    $this->Cell(0,10, "$nombre_campana", 0, 1, 'C');
    //Salto de línea
    $this->Ln(5);
}

//Pie de página
function Footer()
{
    //Posición: a 1,5 cm del final
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Número de página
    $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
}
}

$pdf=new PDF();
$pdf->AliasNbPages(); //para el {nb} del footer
$border = 1;
$font_size_p = 10;
$height_p = 8;
$font_size_h2 = 14;
$height_h2 = 10;
$width_h2 = 50;
$font_size_h1 = 18;
$height_h1 = 15;
$pdf->SetFont('Arial','', $font_size_p);

///PLAN GENERAL DE ACCIONES/////////////////////////////////////////////////
$sql = "SELECT nombre, descripcion, objetivos, objetivos_especificos, concepto, comentarios, fecha_ini, fecha_fin, lugar, presupuesto, beneficios "
       ."FROM crm_campanas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error");
if ($db->sql_numrows($result) > 0)
{
  list($nombre, $descripcion, $objetivos, $objetivos_especificos, $concepto, $comentarios, $fecha_ini, $fecha_fin, $lugar, $presupuesto, $beneficios) 
      = $db->sql_fetchrow($result);
  $pdf->AddPage();
  $pdf->SetFont('','b', $font_size_h1);
  $pdf->Cell(0, $height_h1,"Plan general de acciones", $border, 1, 'C');
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell($width_h2, $height_p, "Nombre de la acción", $border, 0, 'R');
  $pdf->SetFont('','', $font_size_p);
  $pdf->Multicell(0, $height_p, "$nombre", $border, 1);

  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell(0, $height_h2, "Objetivos", $border, 1, 'C');
  $pdf->SetFont('','', $font_size_p);
  $pdf->Multicell(0, $height_p, "$objetivos", $border, 1);
  
  $objetivos_especificos = " · ".str_replace("\n", "\n · ", $objetivos_especificos);
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell(0, $height_h2, "Objetivos Específicos", $border, 1, 'C');
  $pdf->SetFont('','', $font_size_p);
  $pdf->Multicell(0, $height_p, "$objetivos_especificos", $border, 1);
  
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell(0, $height_h2, "Target", $border, 1, 'C');
  $pdf->SetFont('','', $font_size_p);
  $sql = "SELECT target, valor FROM crm_campanas_targets WHERE campana_id='$campana_id' ORDER BY target_id";
  $result = $db->sql_query($sql) or die("Error");
  while (list($target, $valor) = $db->sql_fetchrow($result))
  {
    $targets .= "$target: $valor\n";
  }

  $pdf->Multicell(0, $height_p, "$targets", $border, 1);
  
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell($width_h2, $height_p, "Áreas involucradas", $border, 0, 'R');
  $pdf->SetFont('','', $font_size_p);
  $sql = "SELECT g.name FROM crm_campanas_groups AS c, groups AS g WHERE c.gid=g.gid AND c.campana_id='$campana_id' ORDER BY g.name";
  $result = $db->sql_query($sql) or die("Error");
  while (list($g_name) = $db->sql_fetchrow($result))
  {
    if ($group_index++) $grupos .= ", ";
    $grupos .= "$g_name";
  }
  $pdf->Multicell(0, $height_p, "$grupos", $border, 1);
  
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell($width_h2, $height_p, "Fecha de aplicación", $border, 0, 'R');
  $pdf->SetFont('','', $font_size_p);
  $fecha_ini = date_reverse($fecha_ini);
  $fecha_fin = date_reverse($fecha_fin);
  $pdf->Multicell(0, $height_p, "$fecha_ini - $fecha_fin", $border, 1);
  
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell($width_h2, $height_p, "Lugar", $border, 0, 'R');
  $pdf->SetFont('','', $font_size_p);
  $pdf->Multicell(0, $height_p, "$lugar", $border, 1);
  
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell($width_h2, $height_p, "Presupuesto", $border, 0, 'R');
  $pdf->SetFont('','', $font_size_p);
  $presupuesto = money_format2("%N", $presupuesto);
  $pdf->Multicell(0, $height_p, "$presupuesto", $border, 1);
  
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell($width_h2, $height_p, "Beneficios esperados", $border, 0, 'R');
  $pdf->SetFont('','', $font_size_p);
  $pdf->Multicell(0, $height_p, "$beneficios", $border, 1);
  
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell(0, $height_h2, "Descripción", $border, 1, 'C');
  $pdf->SetFont('','', $font_size_p);
  $pdf->Multicell(0, $height_p, "$descripcion", $border, 1);
  
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell(0, $height_h2, "Concepto", $border, 1, 'C');
  $pdf->SetFont('','', $font_size_p);
  $pdf->Multicell(0, $height_p, "$concepto", $border, 1);
  
  if ($comentarios)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Comentarios", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$comentarios", $border, 1);
  }
}

///INICIATIVAS DE COMUNICACIÓN//////////////////////////////////////////////
$sql = "SELECT actividades_publicidad, medios, eventos, cambios_pagina_web, patrocinios, actividades_rp, marketing_directo, medio_contacto "
       ."FROM crm_campanas_iniciativas_comunicacion WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error");
if ($db->sql_numrows($result) > 0)
{
  list($actividades_publicidad, $medios, $eventos, $cambios_pagina_web, $patrocinios, $actividades_rp, $marketing_directo, $medio_contacto)
      = $db->sql_fetchrow($result);
  $pdf->AddPage();
  $pdf->SetFont('','b', $font_size_h1);
  $pdf->Cell(0, $height_h1,"Iniciativas de comunicación", $border, 1, 'C');
  $pdf->SetFont('','', $font_size_h2);
  $pdf->Cell(0, $height_h2, "Actividades de Publicidad", $border, 1, 'C');
  $pdf->SetFont('','', $font_size_p);
  $pdf->Multicell(0, $height_p, "$actividades_publicidad", $border, 1);

  if ($medios)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell($width_h2, $height_p, "Medios", $border, 0, 'R');
    $pdf->SetFont('','', $font_size_p);
    $medios = str_replace("\n", ", ", $medios);
    $pdf->Multicell(0, $height_p, "$medios", $border, 1);
  }
  
  if ($eventos)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Eventos a realizar", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$eventos", $border, 1);
  }
  
  if ($cambios_pagina_web)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Cambios a Página Web", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$cambios_pagina_web", $border, 1);
  }
  
  if ($patrocinios)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Patrocinios y alianzas", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$patrocinios", $border, 1);
  }
  
  if ($actividades_rp)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Actividades de Relaciones Públicas", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$actividades_rp", $border, 1);
  }

  if ($marketing_directo)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Marketing Directo", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$marketing_directo", $border, 1);
  }

  if ($medio_contacto)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Medio de contacto", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$medio_contacto", $border, 1);
  }
}




///ASPECTOS DE APRENDIZAJE//////////////////////////////////////////////////
$sql = "SELECT actividad, solucion "
       ."FROM crm_campanas_aprendizaje WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error");
if ($db->sql_numrows($result) > 0)
{
  $pdf->AddPage();
  $pdf->SetFont('','b', $font_size_h1);
  $pdf->Cell(0, $height_h1,"Aspectos de aprendizaje", $border, 1, 'C');
  $pdf->Cell(0, 0, '', 0, 0); //necesaria para mandar hasta la derecha
  $x1 = $pdf->GetX(); //el margen de la derecha
  $pdf->Cell(0, 0, '', 0, 1); //retorno de carro
  $half_page = 90;
  $pdf->SetFont('','', $font_size_p);
  while (list($actividad, $solucion) = $db->sql_fetchrow($result))
  {
    $x0 = $pdf->GetX();
    $y0 = $pdf->GetY();
    $pdf->MultiCell($half_page, $height_p, (++$act_index).". ".$actividad, 0);
    $y1 = $pdf->GetY();
    $pdf->SetX($half_page + $x0);
    $pdf->SetLeftMargin($half_page + $x0 + 5);
    $pdf->SetY($y0);
    $pdf->MultiCell(0, $height_p, ($act_index).". ".$solucion, 0);
    if ($pdf->GetY() > $y1) $y1 = $pdf->GetY();

    //dibujar una linea en el margen izquierdo
    $pdf->Line($x0, $y0, $x0, $y1);
    //dibujar una linea en el margen derecho
    $pdf->Line($x1, $y0, $x1, $y1);
    //dibujar una linea para separar en medio
    $pdf->Line($half_page + $x0 + 2, $y0, $half_page + $x0 + 2, $y1);
    //dibujar una linea en el margen de abajo
    $pdf->Line($x0, $y1, $x1, $y1);
    $pdf->SetLeftMargin($x0);
    $pdf->SetX($x0);
    $pdf->SetY($y1);
  }
  
  


}


///PROMOCIÓN DE VENTAS//////////////////////////////////////////////////////
$sql = "SELECT tipo, productos, fecha_ini, fecha_fin, objetivo, mecanica, proceso "
       ."FROM crm_campanas_promociones WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error");
if ($db->sql_numrows($result) > 0)
{
  list($tipo, $productos, $fecha_ini, $fecha_fin, $objetivo, $mecanica, $proceso)
      = $db->sql_fetchrow($result);
  $fecha_ini = date_reverse($fecha_ini);
  $fecha_fin = date_reverse($fecha_fin);
  $pdf->AddPage();
  $pdf->SetFont('','b', $font_size_h1);
  $pdf->Cell(0, $height_h1,"Promoción de ventas", $border, 1, 'C');
  $pdf->SetFont('','', $font_size_h2);
  
  $pdf->Cell($width_h2, $height_p, "Tipo de promoción", $border, 0, 'R');
  $pdf->SetFont('','', $font_size_p);
  $pdf->Multicell(0, $height_p, "$tipo", $border, 1);
  
  if ($productos)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Productos o servicios a promocionar", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$productos", $border, 1);
  }
  
  if ($fecha_ini || $fecha_fin)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell($width_h2, $height_p, "Duración", $border, 0, 'R');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$fecha_ini - $fecha_fin", $border, 1);
  }
  
  if ($objetivo)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Objetivo de ventas adicionales a obtener", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$objetivo", $border, 1);
  }
  
  if ($mecanica)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Mecánica", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$mecanica", $border, 1);
  }

  if ($proceso)
  {
    $pdf->SetFont('','', $font_size_h2);
    $pdf->Cell(0, $height_h2, "Proceso de control de ventas", $border, 1, 'C');
    $pdf->SetFont('','', $font_size_p);
    $pdf->Multicell(0, $height_p, "$proceso", $border, 1);
  }
}
$pdf->Output();

?>
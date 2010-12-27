<?
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
require("$_includesdir/fpdf.php");
define('FPDF_FONTPATH',"$_includesdir/fonts/");

class PDF extends FPDF
{
	//CABECERA DE PAGINA
	function Header()
	{
	    //Logo
	    $this->Image("../img/logo.png", 10, 8, 20, 20);
	    //Arial bold 15
	    $this->SetFont('Arial','B',15);
	    //Movernos a la derecha
	    $this->Cell(25);
	    //Título
	    $this->Cell(0,17, "Cantidad de Cancelaciones de Ventas", 0, 1, 'C');
	    $this->Cell(25);
	    //Salto de línea
	    $this->Ln(8);
	}
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

global $db, $fecha_ini, $fecha_fin,$tipo_rep;
if ($fecha_ini)
{
  $titulo .= " desde $fecha_ini";
  $fecha_ini = date_reverse($fecha_ini);
  $and_fecha .= " AND timestamp>'$fecha_ini 00:00:00'";
}
if ($fecha_fin)
{
  $titulo .= " hasta $fecha_fin";
  $fecha_fin = date_reverse($fecha_fin);
  $and_fecha .= " AND timestamp<'$fecha_fin 23:59:59'";
}
if($tipo_rep ==  2)
{
    include_once("excel_cancelaciones_ventas.php");
}
else
{
    $pdf=new PDF();
    //FORMATO DEL DOCUMENTO PDF
    $pdf->AliasNbPages(); //para el {nb} del footer
    $border = 1;
    $font_type_tit='Arial';
    $font_type_cont='Arial';
    $height_tit = 6;
    $height_cont = 5;
    $font_size_tit = 9;
    $font_size_cont = 8;
    $width=20;
    $width1=35;
    $width2=80;
    $width3=37;

    //SUBTITULO
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell($width);
    $pdf->Cell(190,17,"Cantidad de cancelaciones de ventas por vendedor$titulo.",0,10);

    //TITULO DE LAS TABLAS
    $pdf->SetFont($font_type_tit,'B', $font_size_tit);
    $pdf->SetFillColor(150,150,150);
    $pdf->Cell($width1,$height_tit,'Id concesionaria',$border,0,'L',1);
    $pdf->Cell($width2,$height_tit,'Nombre vendedor',$border,0,'L',1);
    $pdf->Cell($width3,$height_tit,'Total de cancelaciones',$border,1,'L',1);


    ///Cuantas ventas hay concretadas por vendedor/////////////////////////////////////////////////

    $sql = "SELECT u.gid, u.name, count(v.uid) FROM `crm_prospectos_cancelaciones` AS v, users AS u where v.uid=u.uid
    $and_fecha group by (v.uid)";

    $result = $db->sql_query($sql) or die("Error");
    if ($db->sql_numrows($result) > 0)
    {
        while(list($id_concesionaria, $nombre_vendedor, $ventas_concretadas) = $db->sql_fetchrow($result))
        {
            $pdf->SetFont($font_type_cont,'', $font_size_cont);
            $pdf->Cell($width);
            $pdf->Cell($width1, $height_cont,$id_concesionaria, $border, 0, 'R');
            $pdf->Cell($width2, $height_cont,$nombre_vendedor, $border, 0, 'L');
            $pdf->Cell($width3, $height_cont,$ventas_concretadas, $border, 1, 'R');
        }
    }
    $pdf->Output();
}
?>
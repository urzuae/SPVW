<?php
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

$no_archivos_mostrar=5;
$buffer="";
$dir="../bk/";
$array_files=array();
$array_times=array();
$contador=0;
if (is_dir($dir))
{
    $f = opendir($dir) or die ("Error no pude acceder");
    while($fichero=readdir($f))
    {
        $extension=substr($fichero,strlen($fichero)-3 ,strlen($fichero));
        if( (is_file($dir.$fichero)) && ($extension == 'bz2'))
        {
            $array_files[$contador]=$fichero;
            $array_times[$contador]=filemtime($dir.$fichero);
            $contador++;
        }       
    }
    arsort($array_times);  // ordeno los archivos de mayor a menor
    $i=0;   // es para mostrar solo los 3 ultimos
    $array_meses=array('01' => 'Ene','02' => 'Feb','03' => 'Mar','04' => 'Abr','05' => 'May','06' => 'Jun',
                       '07' => 'Jul','08' => 'Ago','09' => 'Sep','10' => 'Oct','11' => 'Nov','12' => 'Dic');
    if(count($array_times) > 0)
    {
        $buffer.="<table align='left' border='0' width='45%'>";
        foreach($array_times as $key => $value)
        {
            if($i < $no_archivos_mostrar)
            {
                $tmp_backup=explode('-',$array_files[$key]);
                $tmp_date=substr($tmp_backup[1],0,8);
                $date_backup=substr($tmp_date,0,4)." - ".$array_meses[substr($tmp_date,4,2)]." - ".substr($tmp_date,6,2);
                $buffer.="<tr style='height:20px'>
                          <td><li><a href='../bk/".$array_files[$key]."' target='_blank'>".$array_files[$key]."</li></td><td>Fecha de creaci&oacute;n&nbsp;&nbsp;".$date_backup."</td>
                          </tr>";
            }
            else
                continue;
            $i++;
        }
        $buffer.="</table>";
    }
    closedir($f);
}

?>

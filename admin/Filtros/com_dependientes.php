<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>CRM</title>
<link
  type="text/css" href="../../themes/grayblue/style.css"
  rel="stylesheet">
<link type="text/css" href="../../themes/grayblue/aqua/theme.css" rel="stylesheet">
<script type="text/javascript" src="../../themes/grayblue/calendar.js">
</script>
<script type="text/javascript" src="../../themes/grayblue/calendar-es.js">
</script>
<script type="text/javascript" src="../../themes/grayblue/calendar-setup.js">
</script>

</head>
<?
 function conexion(){
	if (!($link=mysql_connect("localhost","root","mysql_pwd!"))){
		die("Error conectando a la base de datos.");
	}
	if (!$link=mysql_select_db("crm_prospectos",$link)){ 
		die("Error seleccionando la base de datos.");
	}
	    return($link);
	
  }
$conecta = conexion();
?>
<script src="jquery.js"></script>
<script>
$(document).ready(function(){
	$("select").change(function(){
		// Vector para saber cuál es el siguiente combo a llenar

		var combos = new Array();
		combos['crm_region'] = "crm_zona";
		// Tomo el nombre del combo al que se le a dado el clic por ejemplo: país
		posicion = $(this).attr("name");
		// Tomo el valor de la opción seleccionada 
		valor = $(this).val()		
		// Evaluó  que si es crm_region y el valor es 0, vacié los combos de estado y ciudad
		if(posicion == 'crm_region' && valor==0){
			$("#crm_zona").html('	<option value="0" selected="selected">Selecciona Zona</option>')
			
		}else{
		/* En caso contrario agregado el letreo de cargando a el combo siguiente
		Ejemplo: Si seleccione país voy a tener que el siguiente según mi vector combos es: estado  por qué  combos [país] = estado
			*/
			$("#"+combos[posicion]).html('<option selected="selected" value="0">Cargando...</option>')
			/* Verificamos si el valor seleccionado es diferente de 0 y si el combo es diferente de ciudad, esto porque no tendría caso hacer la consulta a ciudad porque no existe un combo dependiente de este */
			if(valor!="0" || posicion !='nombre'){
			// Llamamos a pagina de combos.php donde ejecuto las consultas para llenar los combos
				$.post("combos.php",{
									combo:$(this).attr("name"), // Nombre del combo
									id:$(this).val() // Valor seleccionado
									},function(data){
													$("#"+combos[posicion]).html(data);	//Tomo el resultado de pagina e inserto los datos en el combo indicado																				
													})												
			}
		}
	})		
})
</script>
<body>
<table align="center">
<form id="form_combo" name="form_combo" action="com_dependientes.php" method="post">

<tr class="row1">
  <td style="width:100px;">Fecha Inicial<?$errorIni;?></td>
  <td style="width:200px;"><input name="fecha_ini" id="fecha_ini" value=""><img src="../../img/calendar.gif" id="f_trigger_c" style="border: 1px solid red; cursor: pointer;" title="Fecha" onmouseover="this.style.background='red';" onmouseout="this.style.background=''"></td>
</tr>

<script>
function update_fecha_fin(cal)
{
  //checamos si es mayor la ini que la fin y cambiar el fin
  var fecha_fin = document.getElementById("fecha_fin").value;
  if (fecha_fin == '') return false;
  var guion_1 = fecha_fin.indexOf("-");
  var guion_2 = fecha_fin.indexOf("-", guion_1 + 1);
  var guion_3 = fecha_fin.length;//fecha_fin.indexOf("-", guion_2 + 1);
  var fin_d = fecha_fin.substring(0, guion_1);
  var fin_m = fecha_fin.substring(guion_1 + 1, guion_2);
  var fin_y = fecha_fin.substring(guion_2 + 1, guion_3);
  var fin  = new Date(fin_y, fin_m - 1, fin_d);
  var ini  = new Date(cal.date.getFullYear(), cal.date.getMonth(), cal.date.getDate());
  if (ini.getTime() > fin.getTime())
  {
   document.getElementById("fecha_fin").value = cal.date.print("%d-%m-%Y");
  }
}

function update_fecha_ini(cal)
{
  //checamos si es mayor la ini que la fin y cambiar el fin
  var fecha_ini = document.getElementById("fecha_ini").value;
  if (fecha_ini == '') return false;
  var guion_1 = fecha_ini.indexOf("-");
  var guion_2 = fecha_ini.indexOf("-", guion_1 + 1);
  var guion_3 = fecha_ini.length;//fecha_ini.indexOf("-", guion_2 + 1);
  var ini_d = fecha_ini.substring(0, guion_1);
  var ini_m = fecha_ini.substring(guion_1 + 1, guion_2);
  var ini_y = fecha_ini.substring(guion_2 + 1, guion_3);
  var ini  = new Date(ini_y, ini_m - 1, ini_d);
  var fin  = new Date(cal.date.getFullYear(), cal.date.getMonth(), cal.date.getDate());
  if (ini.getTime() > fin.getTime())
  {
   document.getElementById("fecha_ini").value = cal.date.print("%d-%m-%Y");
  }
}
Calendar.setup(
{
  inputField :"fecha_ini",
  ifFormat :"%d-%m-%Y",
  onUpdate : update_fecha_fin,
  button : "f_trigger_c"
}
);
</script>

<tr class="row1">
  <td>Fecha final</td>
  <td><input name="fecha_fin" id="fecha_fin" value=""><img src="../../img/calendar.gif" id="f_trigger_d" style="border: 1px solid red; cursor: pointer;" title="Fecha" onmouseover="this.style.background='red';" onmouseout="this.style.background=''"></td>
</tr>
<script>
Calendar.setup(
{
  inputField :"fecha_fin",
  ifFormat :"%d-%m-%Y",
  onUpdate : update_fecha_ini,
  button : "f_trigger_d"
}
);
</script>

<tr class="row1"><td>
Región</td><td>
<select name="crm_region" id="crm_region">
	<option selected="selected" value="0">Selecciona Region</option>
<?
$res = mysql_query("SELECT * FROM crm_regiones order by nombre ASC");
$cant =  mysql_num_rows($res);
if($cant>0){						
	while($rs = mysql_fetch_array($res)){
	?>
		<option value="<?=$rs["region_id"]?>"><?=$rs["nombre"]?></option>
	<? 
	}
} 
?>
</select></td>
</tr>

<tr class="row1"><td>
Zona</td>
<td>
<select id="crm_zona" name="crm_zona">
	<option value="0" selected="selected">Selecciona Zona</option>
</select>
</td>
</tr>
<tr class="row1"><td>
Concesionaria</td>
<td>
<select id="concesion" name="concesion">
	<option value="0" selected="selected">Selecciona Concesionaría</option>
	<?
$res = mysql_query("SELECT * FROM groups order by name ASC");
$cant =  mysql_num_rows($res);
if($cant>0){						
	while($rs = mysql_fetch_array($res)){
	?>
		<option value="<?=$rs["name"]?>"><?=$rs["name"]?></option>
	<? 
	}
} 
?>
</select>
</td>
</tr>

<tr class="row1"><td>
Fuente</td>
<td>
<select id="fuente" name="fuente">
	<option value="0" selected="selected">Selecciona Fuente</option>
	<?
$res = mysql_query("SELECT * FROM crm_contactos_origenes order by nombre ASC");
$cant =  mysql_num_rows($res);
if($cant>0){						
	while($rs = mysql_fetch_array($res)){
	?>
		<option value="<?=$rs["origen_id"]?>"><?=$rs["nombre"]?></option>
	<? 
	}
} 
?>
</select>
</td>
</tr>

<tr class="row1"><td>
Modelo</td>
<td>
<select id="modelo" name="modelo">
	<option value="0" selected="selected">Selecciona Modelo</option>
	<?
$res = mysql_query("SELECT DISTINCT(modelo) FROM crm_prospectos_unidades order by modelo ASC");
$cant =  mysql_num_rows($res);
if($cant>0){						
	while($rs = mysql_fetch_array($res)){
	?>
		<option value="<?=$rs["modelo"]?>"><?=$rs["modelo"]?></option>
	<? 
	}
} 
?>
</select>
</td>
</tr>
<tr class="row2">
  <td colspan="2" align="center"><input type="submit" name="submit" value="Aceptar"><input type="button" value="Regresar" onclick="location.href='index.php?_module=$_module';"></td>
</tr>
</form>
</table>

<?php
if($_REQUEST['submit']){
	if($_REQUEST['fecha_ini']=='')
	$errorIni="Estas maaaaaal";

echo "<pre>";
print_r($_REQUEST);
}
?>

</body>
</html>



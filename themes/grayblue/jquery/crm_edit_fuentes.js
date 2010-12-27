/* 
 * Script que sirve  para administrar el catalogo de fuentes
 */
var url_script_edit ="index.php?_module=Catalogos&_op=edit_fuentes";
var padre_id;
var origen_id;
var fuente_padre;
var fecha_ini;
var fecha_fin;
var fuente;
var zona_id;
var id_gid;
var nm_gid;
$(document).ready(function (){
// las siguientes funciones es para la busquedad de fuentes
    $("#actualizar_fuente").click(function(){
        origen_id = $("#origen_id").val();
        fuente   = $("#fuente").val();
        fecha_ini= $("#fecha_ini").val();
        fecha_fin= $("#fecha_fin").val();

        // decido de quien va hacer padre de la nueva fuente
        if( (fuente != '') && (origen_id != 0) && (fecha_ini != '') && (fecha_fin != '') )
        {
            $.post(url_script_edit,{opc:1,padre_id:origen_id,fuente:fuente,fecha_ini:fecha_ini,fecha_fin:fecha_fin},function(data){
                if(data == 'Actualizado')
                {
                    $("#resultado").css({color: "#04B45F", background: "#FFFFFF"});
                    $("#resultado").html(data);
                    location.href = "index.php?_module=Catalogos&_op=find&origen="+origen_id+"&buscar_fuentes=1";
                }
                else
                {
                    $("#resultado").css({color: "#FE2E2E", background: "#FFFFFF"});
                    $("#resultado").html(data);
                }
            });
         }
        else
        {
             alert("Favor de llenar todos los campos, son obligatorios");
        }
    
    })
});


function Bloquea_Fuentes(id)
{
    $.get(url_script_edit,{opc:2,padre_id:id},function(data){
        $("#resultado").html(data);
        location.href = "index.php?_module=Catalogos&_op=find&origen="+id+"&buscar_fuentes=1";
    })
}
function Desbloquea_Fuentes(id)
{
    $.get(url_script_edit,{opc:3,padre_id:id},function(data){
        $("#resultado").html(data);
        location.href = "index.php?_module=Catalogos&_op=find&origen="+id+"&buscar_fuentes=1";
    })
}
/* 
 * Script que sirve  para administrar el catalogo de fuentes
 */
var url_script_edit ="index.php?_module=Catalogos&_op=edit_fuentes";
var url_script ="index.php?_module=Catalogos&_op=admin_fuentes";
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
    $("#paso2").hide();
    // Codigo para guardar la fuente
    $("#guardar").click(function(){
        padre_id = $("#padre_id").val();
        origen_id= $("#origen").val();
        fuente   = $("#fuente").val();
        fecha_ini= $("#fecha_ini").val();
        fecha_fin= $("#fecha_fin").val();

        // decido de quien va hacer padre de la nueva fuente
        if( (origen_id != 0) && (padre_id != 0) )
            fuente_padre=origen_id;
        else
            fuente_padre=padre_id;

        if( (fuente != '') && (fuente_padre != 0) && (fecha_ini != '') && (fecha_fin != '') )
        {
            $.post(url_script,{opc:1,padre_id:fuente_padre,fuente:fuente,fecha_ini:fecha_ini,fecha_fin:fecha_fin},function(data){
                if(data == 'Guardado')
                {
                    $("#resultado").css({color: "#04B45F", background: "#FFFFFF"});
                    $("#resultado").html(data);
                    $("#guardar").attr('disable',true);
                    $("#paso2").show();
                }
                else
                {
                    $("#resultado").css({color: "#FE2E2E", background: "#FFFFFF"});
                    $("#resultado").html(data);
                    alert("La fuente ya se encuentra registrada")
                }
            });
         }
        else
        {
             alert("Favor de llenar todos los campos, son obligatorios");
        }
     })

     // Codigo para actualizar el permiso para las fuentes
     $("#zona_id").change(function(){
        zona_id=$("#zona_id").val();
        if(zona_id >0)
        {
            $.get(url_script,{opc:2,zona_id:zona_id},function(data){
                $("#gids").html(data);
            })
        }
     })

     // codigo para buscar concesionaria de acuerdo al id y nombre de la concesionaria
     $("#busca_gids").click(function(){
        id_gid=$("#bus_gid").val();
        nm_gid=$("#bus_nom").val();
        $.post(url_script,{opc:3,id_gid:id_gid,nm_gid:nm_gid},function(data){
            $("#gids").html(data);
        })
     })

    $("#marcar").click(function(){
        $('input').each(function(i, item){
            $(item).attr('checked', true);
        });
    });
    $("#desmarcar").click(function(){
            $('input').each(function(i, item){
            $(item).attr('checked', false);
        });
    });
    $("#guardar_gid").click(function(){
        cadena_filtros='';
        fuente   = $("#fuente").val();
        $('input:checkbox:checked').each(function(i, item){
            cadena_filtros+=$(item).val()+"|";});
            document.getElementById("seleccionados").value=cadena_filtros;
            $.post(url_script,{opc:4,fuente:fuente,gids:cadena_filtros},function(data){
                $("#gids").html(data);
            })
    });


});



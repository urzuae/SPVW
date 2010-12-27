var gid=0;
var fase=0;
var url_tipo_pagos ="index.php?_module=Gerente&_op=regresa_tipo_pagos";
var url_vendedores ="index.php?_module=Gerente&_op=regresa_vendedores";
var url = "index.php?_module=Gerente&_op=elimina_registros";
var url_carga="index.php?_module=Gerente&_op=cargar_registros";
var url_carga_concesionarias="index.php?_module=Ajax&_op=cargar_concesionarias";
var url_nombre_concesionarias="index.php?_module=Ajax&_op=regresa_concesionaria";
var url_carga_vendedores="index.php?_module=Ajax&_op=cargar_vendedores";
var fecha_ini='';
var fecha_fin='';
var evento_id=0;
var array_gid=new Array()
var array_uid=new Array() 
var contador_gid=0;
var contador_uid=0;

$(document).ready(function (){
    $(".tablesorter").tablesorter();

    // CODIGO PARA SACAR LOS TIPOS DE PAGOS, CUANDO LA FASE SEA 3
    $("#fase_id").change(function(event){
        fase=$("#fase_id").val();
        if(fase == 3)
        {
            $.get(url_tipo_pagos,{fase_id:fase},function(data){
                $("#tipo_pago_id").html(data);
            });
        }
        else
        {
            $("#tipo_pago_id").html('<option value="0" selected="selected"></option>')
        }
    });

    // CODIGO QUE BUSCA LOS VENDEDORES DE LA CONCESIONARIA DADA COMO PARAMETRO
    $("#tmp_gid").change(function(event){
        gid=$("#tmp_gid").val();
        if(gid > 0)
        {
            $.get(url_vendedores,{gid:gid},function(data){
                $("#tmp_uid").html(data);
            });
        }
        else
        {
            $("#tmp_uid").html('<option value="0" selected="selected"></option>')
        }
    });

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

    $("#boton_checks").click(function(){
        fecha_ini=$("#cfecha_ini").val()
        fecha_fin=$("#cfecha_fin").val()
        evento_id=$("#cevento_id").val();
        if(confirm("Desea eliminar los registros seleccionados  "))
        {
            cadena_filtros='';
            $('input:checked').each(function(i, item){
                cadena_filtros+=$(item).val()+"|";
            });
            $.post(url,{contactos:cadena_filtros},function(data){
            location.href ="index.php?_module=Gerente&_op=filtros&fecha_ini="+fecha_ini+"&fecha_fin="+fecha_fin+"&evento_id="+evento_id+"&submit=submit";
            })
        }
    });

    $("#listado_gid").dblclick(function(e){
        $.get(url_nombre_concesionarias,{gid:$("#listado_gid").val()},function(data){
            $("#listado_gid_p").append(data);
            array_gid[contador_gid]=$("#listado_gid").val();
            contador_gid++;
        });

        $.get(url_carga_concesionarias,{gid:$("#listado_gid").val()},function(data){
            $("#listado_uid").html(data);
        });
    });

    $("#listado_uid").dblclick(function(e){
        $.get(url_carga_vendedores,{gid:$("#listado_gid").val(),uid:$("#listado_uid").val()},function(data){
            $("#listado_uid_p").append(data);
            array_uid[contador_uid]=$("#listado_uid").val();
            contador_uid++;
        })
    });


// codigo para eliminar una opcion del select de concesionarias y vendedores
    $("#listado_gid_p").dblclick(function(e){
        tmp_gid=$("#listado_gid_p").val();
        id_g=0;
        if(array_gid.length>0)
        {
            for(j=0; j < array_gid.length; j++)
            {
               if(parseInt(array_gid[j]) == parseInt(tmp_gid))
                   id_g = j;
            }
            array_gid[id_g]=0;            
        }
        $("#listado_gid_p").find("option[value='"+tmp_gid+"']").remove();
    });
// elimina vendedores

    $("#listado_uid_p").dblclick(function(e){
        tmp_uid=$("#listado_uid_p").val();
        id_u=0;
        if(array_uid.length>0)
        {
            for(j=0; j < array_uid.length; j++)
            {
               if(parseInt(array_uid[j]) == parseInt(tmp_uid))
                    id_u = j;
            }
            array_uid[id_u]=0;
        }
        $("#listado_uid_p").find("option[value='"+tmp_uid+"']").remove();
    });


    $("#submit").click(function(e){
        tmp_gids='';
        tmp_uids='';
        if(array_gid.length>0)
            tmp_gids=array_gid.join();
        if(array_uid.length>0)
            tmp_uids=array_uid.join();

        document.getElementById('gids').value=tmp_gids;
        document.getElementById('uids').value=tmp_uids;
    });
});
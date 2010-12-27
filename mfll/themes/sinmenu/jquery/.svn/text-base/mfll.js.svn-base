var gid=0;
var fase=0;
var url_tipo_pagos ="index.php?_module=Gerente&_op=regresa_tipo_pagos";
var url_vendedores ="index.php?_module=Gerente&_op=regresa_vendedores";
var url = "index.php?_module=Gerente&_op=elimina_registros";
var url_carga="index.php?_module=Gerente&_op=cargar_registros";
var url_tabla="index.php?_module=Registro&_op=genera_tabla";
var fecha_ini='';
var fecha_fin='';
var evento_id=0;
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
    $("#cargar").click(function(){
        fecha_ini=$("#cfecha_ini").val()
        fecha_fin=$("#cfecha_fin").val()
        evento_id=$("#cevento_id").val();
        if(confirm("Esta seguro de subir los registros al Sistema de Prospección VW"))
        {
            $.get(url_carga,{id:1},function(data){
            location.href ="index.php?_module=Gerente&_op=filtros&fecha_ini="+fecha_ini+"&fecha_fin="+fecha_fin+"&evento_id="+evento_id+"&submit=submit";
            });
        }
    });
    $("#concesionaria").change(function(e){
        $.get(url_tabla,{gid:$("#concesionaria").val()},function(data){
            $("#tabla").html(data);
        });
    });

});
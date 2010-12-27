var gid=0;
var fase=0;
var url_tipo_pagos ="index.php?_module=Reportes&_op=regresa_tipo_pagos";
var url_vendedores ="index.php?_module=Reportes&_op=regresa_vendedores";

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

});
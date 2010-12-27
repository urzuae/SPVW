/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var idFont = 0;
$(document).ready(function(){

    //Añadir el campo para el nombre de la fuente
    $("#addFont").click(function(){
        var contentHtml = "<tr class='nuevoFuenteHijo' height='10'><td colspan='3' style='color:#3e4f88;font-weight:bold;'>Agregue la fuente y teclee el periodo en que estara activa</td></tr><tr><td>Fuente hijo</td><td><input class='requiredChild' type='text' name='nombreFuenteHijo' id='nombreFuenteHijo' value=''></td><tr>";
        $("#fecha_i").val("0000-00-00 00:01:01");
        $("#fecha_c").val("0000-00-00 23:59:59");
        $("#updateFont tbody tr.nuevoFuenteHijo").remove();
        $("#updateFont tbody tr.padre").after(contentHtml);
        $("#flagAddChild").attr("value", "1");
    });

    // Salva los datos guardados
    
    $("#saveFont").click(function(){
        idFont =  $("#idFuente").val();
        if($("#nombreFuente").attr("value").length < 2)
        {
            alert("El nombre de la fuente debe tener al menos dos caracteres");
            return false;
        }
        if($("#flagAddChild").val() == "1")
            if($("#nombreFuenteHijo").attr("value").length < 2)
            {
                alert("El nombre de la fuente debe tener al menos dos caracteres");
                return false;
            }
        $("#flagAddChild").attr("value", "0");
        location.href ="index.php?_module=Catalogos&_op=mostrarArbol&padre_id=" +
        idFont + "&guardar=1&nombrePadre=" + $("#nombreFuente").attr("value") +
        "&nombreHijo=" + $("#nombreFuenteHijo").attr("value")+"&fecha_i="+$("#fecha_ini").val()+
        "&fecha_c="+$("#fecha_fin").val();
    });

    //Elima fuente
    $("#deleteFont").click(function(){
        idFont =  $("#idFuente").val();
        if(!confirm("¿Realmente desea eliminar la fuente?"))
            return false;
        location.href ="index.php?_module=Catalogos&_op=mostrarArbol&padre_id=" + idFont + "&del=1";        
    });

    //Bloquea la fuente
    $("#updFont").click(function(){
        idFont =  $("#idFuente").val();
        if(!confirm("¿Realmente desea bloquear la fuente?"))
            return false;
        location.href ="index.php?_module=Catalogos&_op=mostrarArbol&padre_id=" + idFont + "&upd=1";
    });

    //Desbloquea la fuente
    $("#upddesFont").click(function(){
        idFont =  $("#idFuente").val();
        if(!confirm("¿Realmente desea desbloquear la fuente?"))
            return false;
        location.href ="index.php?_module=Catalogos&_op=mostrarArbol&padre_id=" + idFont + "&upddes=1";
    });
});

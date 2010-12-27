var urlColonias  ="index.php?_module=Directorio&_op=Consulta_cp";
var cb;
$(document).ready(function (){
        $("#colonia").jecKill();
        $("#colonia").jec();
	$("#cp").blur(function(event){
            $("#colonia").jecKill();
            $("#colonia").jec();
            if($("#cp").val() != '')
            {
                $.get(urlColonias,{cp:$('#cp').val()},function(data){
                    $("#colonia").html(data);
                    $("#colonia").jecKill();
                    $("#colonia").jec();
                })
            }
        event.preventDefault();
	})
});
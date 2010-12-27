<?php
$buffer_filtros="
        <table width=\"100%\" height=\"190px\" border=\"0\" align=\"center\">
            <thead>
            <tr height='25'>
              <td colspan=\"3\" align=\"center\">Opciones de reporte</td>
            </tr>
            </thead>
              <tr>
              <td colspan=\"3\" align=\"center\">&nbsp;</td>
            </tr>

            <tr class=\"row1\">
                <td width=\"50%\" valign=\"top\" >

            <table width='100%' border='0'>
                <thead>
                    <tr height='22'><td colspan=2 >Tipo de grafica</td></tr>
                </thead>
                <tbody>
                    <tr><td>Tipo de grafica:</td><td>".$select_grafico."</td></tr>
                </tbody>
                <thead>
                    <tr><td colspan=\"2\">&nbsp;Fecha de Importaci&oacute;n</td></tr>
                </thead>
                <tbody>
                    <tr class=\"row1\">
                    <td style=\"width:100px;\">Fecha de inicio</td>
                    <td style=\"width:200px;\"><input name=\"fecha_ini\" id=\"fecha_ini\" value=\"$fecha_ini\"><img src=\"../img/calendar.gif\" id=\"f_trigger_c\" style=\"border: 1px solid red; cursor: pointer;\" title=\"Fecha\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\"></td>
                    </tr>
                    <script>
                    function update_fecha_fin(cal)
                    {
                      //checamos si es mayor la ini que la fin y cambiar el fin
                        var fecha_fin = document.getElementById('fecha_fin').value;
                        if (fecha_fin == '')
                            return false;
                        var guion_1 = fecha_fin.indexOf('-');
                        var guion_2 = fecha_fin.indexOf('-', guion_1 + 1);
                        var guion_3 = fecha_fin.length;//fecha_fin.indexOf('-', guion_2 + 1);
                        var fin_d = fecha_fin.substring(0, guion_1);
                        var fin_m = fecha_fin.substring(guion_1 + 1, guion_2);
                        var fin_y = fecha_fin.substring(guion_2 + 1, guion_3);
                        var fin  = new Date(fin_y, fin_m - 1, fin_d);
                        var ini  = new Date(cal.date.getFullYear(), cal.date.getMonth(), cal.date.getDate());
                        if (ini.getTime() > fin.getTime())
                        {
                            document.getElementById('fecha_fin').value = cal.date.print('%Y-%m-%d');
                        }
                    }
                    function update_fecha_ini(cal)
                    {
                        //checamos si es mayor la ini que la fin y cambiar el fin
                        var fecha_ini = document.getElementById('fecha_ini').value;
                        if (fecha_ini == '')
                            return false;
                        var guion_1 = fecha_ini.indexOf('-');
                        var guion_2 = fecha_ini.indexOf('-', guion_1 + 1);
                        var guion_3 = fecha_ini.length;//fecha_ini.indexOf('-', guion_2 + 1);
                        var ini_d = fecha_ini.substring(0, guion_1);
                        var ini_m = fecha_ini.substring(guion_1 + 1, guion_2);
                        var ini_y = fecha_ini.substring(guion_2 + 1, guion_3);
                        var ini  = new Date(ini_y, ini_m - 1, ini_d);
                        var fin  = new Date(cal.date.getFullYear(), cal.date.getMonth(), cal.date.getDate());
                        if (ini.getTime() > fin.getTime())
                        {
                            document.getElementById('fecha_ini').value = cal.date.print('%Y-%m-%d');
                        }
                    }
                    Calendar.setup(
                    {
                        inputField :'fecha_ini',
                        ifFormat :'%Y-%m-%d',
                        onUpdate : update_fecha_fin,
                        button : 'f_trigger_c'
                    }
                    );
                    </script>
                    <tr class=\"row1\">
                      <td>Fecha de fin</td>
                      <td><input name=\"fecha_fin\" id=\"fecha_fin\" value=\"$fecha_fin\"><img src=\"../img/calendar.gif\" id=\"f_trigger_d\" style=\"border: 1px solid red; cursor: pointer;\" title=\"Fecha\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\"></td>
                    </tr>
                    <tbody>
                    <script>
            Calendar.setup(
            {
              inputField :'fecha_fin',
              ifFormat :'%Y-%m-%d',
              onUpdate : update_fecha_ini,
              button : 'f_trigger_d'
            }
            );
            </script>
                    <tr>
                        <td colspan=\"2\">
                            <table id=\"displayFilter\" style=\"text-align: center; width: 100%;\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\">
                            <thead>
                            <tr>
                                <td colspan=2 align=\"left\">&nbsp;&nbsp;Filtro por vehiculo</td>
                            </tr>
                            </thead>
                            <tbody class=\"filterVehicle row1\">
                            <tr class=\"showUnited\">
                                <td>Tipo de vehiculo</td>
                                <td class='list row1'><select style='width: 200px;' name='listVersion' id='listVersion'><option value='0'>Todos</option></select></td>
                            </tr>
                            <tr class=\"showVersion row2\">
                                <td>Version</td>
                                <td class='list row2'><select style='width: 200px;' name='listVersion' id='listVersion'><option value='0'>Todos</option></select></td>
                            </tr>
                            <tr class=\"showTransmision row1\">
                                <td>Transmicion</td>
                                <td class='list row1'><select style='width: 200px;' name='listVersion' id='listVersion'><option value='0'>Todos</option></select></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            </tbody>
                            <thead>
                            <tr>
                                <td colspan=2 align='left'>&nbsp;Fuente</td>
                            </tr>
                            </thead>
                            <tr class=\"row1\"><td>Fuente Padre</td><td>".$select_origenPadre."</td></tr>
                            </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width=\"50%\" valign=\"top\" >
                  <table width=\"100%\" border=\"0\">
                    <thead>
                        <tr>
                        <td colspan=2 >&nbsp;Grupo Empresarial</td>
                        </tr>
                    </thead>
                        <tr class=\"row1\"><td width=\"22%\">Grupo</td><td>".$select_empresarial."</td></tr>
                    </table>
                    <div id=\"ubicacion\">
                    <table width=\"100%\" border=\"0\" align=\"center\">
                        <thead>
                        <tr>
                        <td colspan=2 >&nbsp;Ubicación</td>
                        </tr>
                        </thead>
                        <tr class=\"row1\"><td>Regi&oacute;n</td><td>".$select_regiones."</td></tr>
                        <tr class=\"row1\"><td>Zona</td><td>".$select_zonas."</td></tr>
                        <tr class=\"row1\"><td>Entidad</td><td>".$select_entidad."</td></tr>
                        <tr class=\"row1\"><td>Plaza</td><td>".$select_plaza."</td></tr>
                        <tr class=\"row1\"><td>Concesionaria</td><td>".$select_concesion."</td></tr>
                    </table>
                    </div>
                    <table width=\"100%\"  border=\"0\" align=\"center\">
                        <thead>
                        <tr>
                        <td colspan=2 >Nivel de Concesionaria</td>
                        </tr>
                        </thead>
                        <tr class=\"row1\"><td>Nivel</td><td>$select_categoria</td></tr>
                    </table>
                    </td>
                </tr>
                <thead><tr>
                     <td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"filterVehicle\"  id='filterVehicle' value=\"Generar Gráfica\"></td>
                </tr></thead>
               </table>
";


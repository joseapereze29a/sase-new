<?
session_start();
##############################################################################
### Construyo codacta, dicese de Libreria / Funcion                        ###
### (c) 2.003 Utilicese con precaucion, mal uso de estas lineas pueden que ###
### empiece a presentar sintomas como: dolor en la naris por concentracion ###
### de mocos, cojonera, dolor entre las nalgas, pestilencia, flatulencia,  ###
### irritacion en las manos, callos en las manos, ojos vidriosos, etc.     ###
##############################################################################

### Esta funcion busca los datos del Profesor, basados en la "cedula_profesor"


function datos_profesor($ci_profesor, $conexion)
{
    $nombres_apellidos_prof = '';

    // Sanitizar entrada para evitar inyección (simple, mejor usar prepared statements si es posible)
    $ci_profesor = mysqli_real_escape_string($conexion, $ci_profesor);

    // Consulta para contar coincidencias
    $sqlcmd = "SELECT COUNT(*) AS cantidad FROM profesores_cippsv WHERE cedula_profesor='$ci_profesor'";

    $result = mysqli_query($conexion, $sqlcmd);
    if ($result) {
        $registro = mysqli_fetch_object($result);
        $cantidad = $registro ? $registro->cantidad : 0;
        mysqli_free_result($result);
    } else {
        $cantidad = 0;
    }

    if ($cantidad < 1) {
        // No hay coincidencias
        $nombres_apellidos_prof = '';
    } else {
        // Obtener datos del profesor
        $sqlcmd = "SELECT apellidos_nombres, nombres FROM profesores_cippsv WHERE cedula_profesor='$ci_profesor'";
        $result = mysqli_query($conexion, $sqlcmd);
        if ($result) {
            $registro = mysqli_fetch_object($result);
            if ($registro) {
                $apellidos_nombres = $registro->apellidos_nombres;
                $nombres = $registro->nombres;
                if ($nombres) {
                    $nombres_apellidos_prof = $nombres . ' ' . $apellidos_nombres;
                } else {
                    $nombres_apellidos_prof = $apellidos_nombres;
                }
            }
            mysqli_free_result($result);
        }
    }

    return $nombres_apellidos_prof;
}

### Fin de la Funcion ########################################################
?>
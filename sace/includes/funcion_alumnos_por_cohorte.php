<?php
session_start();

/**
 * Calcula la cantidad de alumnos que pertenecen a una cohorte específica.
 * Requiere una conexión mysqli válida en la variable global $conexion.
 *
 * @param string $codcohorte
 * @return int
 */
function alumnos_por_cohorte($codcohorte) {
    global $conexion;

    $cantidad_actas = 0;
    $cantidad_multiactas = 0;
    $numero_alumnos = 0;

    // Verifico que existan actas
    $sqlcmd = "SELECT COUNT(*) AS cantidad_actas FROM registro_actas WHERE codcohorte='$codcohorte'";
    $res = mysqli_query($conexion, $sqlcmd);
    if ($row = mysqli_fetch_object($res)) {
        $cantidad_actas = (int)$row->cantidad_actas;
    }

    // Verifico que existan multiactas
    $sqlcmd = "SELECT COUNT(*) AS cantidad_multiactas FROM multiactas WHERE codcohorte='$codcohorte'";
    $res = mysqli_query($conexion, $sqlcmd);
    if ($row = mysqli_fetch_object($res)) {
        $cantidad_multiactas = (int)$row->cantidad_multiactas;
    }

    if ($cantidad_actas > 0 || $cantidad_multiactas > 0) {
        // Creo tabla temporal
        $sqlcmd = "CREATE TEMPORARY TABLE alumnos_por_cohorte_temp (
                        cedula INT(10) UNSIGNED NOT NULL,
                        KEY (cedula)
                   ) TYPE=MyISAM";
        mysqli_query($conexion, $sqlcmd);

        // Inserto cédulas de actas
        $sqlcmd = "SELECT codacta FROM registro_actas WHERE codcohorte='$codcohorte'";
        $res = mysqli_query($conexion, $sqlcmd);
        while ($row = mysqli_fetch_object($res)) {
            $codacta = $row->codacta;
            $sqlcmd2 = "INSERT INTO alumnos_por_cohorte_temp (cedula)
                        SELECT cedula FROM record_notas WHERE codacta='$codacta'";
            mysqli_query($conexion, $sqlcmd2);
        }

        // (Opcional: incluir también las multiactas)
        $sqlcmd = "SELECT codacta FROM multiactas WHERE codcohorte='$codcohorte'";
        $res = mysqli_query($conexion, $sqlcmd);
        while ($row = mysqli_fetch_object($res)) {
            $codacta = $row->codacta;
            $sqlcmd2 = "INSERT INTO alumnos_por_cohorte_temp (cedula)
                        SELECT cedula FROM record_notas WHERE codacta='$codacta'";
            mysqli_query($conexion, $sqlcmd2);
        }

        // Contar alumnos únicos
        $sqlcmd = "SELECT COUNT(DISTINCT cedula) AS total FROM alumnos_por_cohorte_temp";
        $res = mysqli_query($conexion, $sqlcmd);
        if ($row = mysqli_fetch_object($res)) {
            $numero_alumnos = (int)$row->total;
        }

        // Eliminar tabla temporal
        $sqlcmd = "DROP TABLE alumnos_por_cohorte_temp";
        mysqli_query($conexion, $sqlcmd);
    }

    return $numero_alumnos;
}
?>
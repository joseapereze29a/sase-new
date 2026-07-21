<?

###
### Los Clasicos Includes
###

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

#ini_set('max_execution_time', 900);

inicializarElArchivoDeNotas();


$sqlcmd = "SELECT dc.codsede, dc.modalidad, dc.ciudad, dc.edo_prov, oe.codopest, oe.tipo, oe.mencion_especialidad, "
		. "co.codcohorte, co.fecha_inicio, co.periodo_lectivo "
		. "FROM directorio_cippsv dc, oportunidades_estudio oe, cohortes co "
		. "WHERE dc.codsede=oe.codsede AND dc.codsede=co.codsede AND oe.codopest=co.codopest AND dc.codsede='$_codsede' ";


$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

writeToLogFile ("partida..."); 

#echo "esto debe matar<BR><BR><BR><BR><BR> el enca<BR><BR>";
$nu=0;

while ($registro = mysql_fetch_object($query))
{
	$codsede = $registro->codsede;
	$modalidad = $registro->modalidad;
	$ciudad = $registro->ciudad;
	$edo_prov = $registro->edo_prov;
	$codopest = $registro->codopest;
	$tipo = $registro->tipo;
	$mencion_especialidad = $registro->mencion_especialidad;
	$codcohorte = $registro->codcohorte;
	$fecha_inicio = $registro->fecha_inicio;
	$periodo_lectivo = $registro->periodo_lectivo;

	$alumnos = "";
$nu++;
###	writeToLogFile("por la corte numero: $nu");

	$alumnos = buscarCedulaPorCohorte ($registro);
	
	agregarInformacionAlArchivoDeNotas($alumnos);

}

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="alumnos.csv"');
readfile('/var/www/sace/consultas/alumnos.csv');

#echo "done! ";

writeToLogFile ("done !");

#10061222

function buscarCedulaPorCohorte ($registro)
{

	$alumnos = "";
	
	$sqlcmd = "SELECT rn.cedula FROM cohortes co, registro_actas ra, record_notas rn "
			. "WHERE co.codcohorte='" . $registro->codcohorte . "' AND co.codcohorte=ra.codcohorte AND "
			. "ra.codacta=rn.codacta GROUP BY rn.cedula order by rn.cedula ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro2 = mysql_fetch_object($query))
	{
		$nombres = "";
		$apellidos = "";
		$nombreCompleto = "";
		
		$datosPersonales = buscarDatosPersonalesPorCedula ( $registro2->cedula );
		
		$indiceAcademico = buscarIndiceAcademicoDeEstudiante ( $registro, $registro2->cedula );
		
		$alumnos .= formatearContenido( $registro->modalidad ) . ",";
		$alumnos .= formatearContenido( $registro->ciudad ) . ",";
		$alumnos .= formatearContenido( $registro->edo_prov ) . ",";
		$alumnos .= formatearContenido( $registro->tipo ) . ",";
		$alumnos .= formatearContenido( $registro->mencion_especialidad ) . ",";
		$alumnos .= formatearContenido( $registro->codsede ) . ",";
		$alumnos .= formatearContenido( $registro->codopest ) . ",";
		$alumnos .= formatearContenido( $registro->codcohorte ) . ",";
		$alumnos .= formatearContenido( $registro->fecha_inicio ) . ",";
		$alumnos .= formatearContenido( $registro->periodo_lectivo ) . ",";
		$alumnos .= formatearContenido( $registro2->cedula ) . ",";
		
		if ( ($datosPersonales->nombres) && ($datosPersonales->nombres != "") && ($datosPersonales->nombres != null) ) $nombres = $datosPersonales->nombres;
		if ( ($datosPersonales->apellidos) && ($datosPersonales->apellidos != "") && ($datosPersonales->apellidos != null) ) $apellidos = $datosPersonales->apellidos;


		$nombres = strtolower($nombres);
		$apellidos = strtolower($apellidos);
		
		if ( ($apellidos) AND ($nombres) ) 			$nombreCompleto = ucwords($nombres) . ' ' . ucwords($apellidos);
		if ( ($apellidos) AND (! $nombres) ) 		$nombreCompleto = ucwords($apellidos);
		if ( (! $apellidos) AND ($nombres) ) 		$nombreCompleto = ucwords($nombres);
		if ( (! $apellidos) AND (! $nombres) )		$nombreCompleto = 'No Existe Registro';
		
		$alumnos .= formatearContenido( $nombreCompleto ) . ",";

		$alumnos .= formatearContenido( $indiceAcademico ) . "\r\n";

	}
	
	return $alumnos;

}

function buscarDatosPersonalesPorCedula ($cedula)
{

	$sqlcmd = "SELECT apellidos, nombres, fecha_nacimiento, lugar_nacimiento, nacionalidad, sexo, "
			. "profesion_oficio, estado_civil FROM datos_personales WHERE cedula='$cedula' LIMIT 1 ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	if ($registro3 = mysql_fetch_object($query))
	{
		return $registro3;
	}
		
}



function buscarIndiceAcademicoDeEstudiante ( $registro, $cedula )
{

	$notas = "";
	$total_creditos = "";


	$sqlcmd1 = "SELECT codasig, asignatura, creditos, periodos, codasig_imp "
			 . "FROM pensum_estudios "
			 . "WHERE codsede='" . $registro->codsede . "' AND pensum_estudios.codopest='" . $registro->codopest . "' AND pensum_estudios.status='Activa' "
			 . "ORDER BY periodos, codasig ";

	$query1 = mysql_db_query(DB_DATABASE,"$sqlcmd1");

	while ($registro4 = mysql_fetch_object($query1))
	{
		$codasig = $registro4->codasig;
		$asignatura = $registro4->asignatura;
		$creditos = $registro4->creditos;
		$periodos = $registro4->periodos;
		$codasig_imp = $registro4->codasig_imp;


		$sqlcmd2 = "SELECT count(*) AS cantidad "
				 . "FROM registro_actas, record_notas "
				 . "WHERE registro_actas.codcohorte='" . $registro->codcohorte . "' AND registro_actas.codasig='$codasig' AND "
				 . "record_notas.cedula='$cedula' AND registro_actas.codacta=record_notas.codacta ";

		$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

		if ($registro5 = mysql_fetch_object($query2))
		{
			$cantidad_registro_actas = $registro5->cantidad;
		}
		

		if ($cantidad_registro_actas > 0)
		{

			$sqlcmd3 = "SELECT record_notas.codacta, record_notas.calificacion "
					 . "FROM registro_actas, record_notas "
					 . "WHERE registro_actas.codcohorte='" . $registro->codcohorte . "' AND registro_actas.codasig='$codasig' AND "
					 . "record_notas.cedula='$cedula' AND registro_actas.codacta=record_notas.codacta "
					 . "ORDER BY codacta ";

			$query3 = mysql_db_query(DB_DATABASE,"$sqlcmd3");

			while ($registro6 = mysql_fetch_object($query3))
			{
				$codacta = $registro6->codacta;
				$calificacion = $registro6->calificacion;
				#$arreglo_calificiaciones[] = $registro6->calificacion;
				
				if ( ($calificacion >= 1) AND ($calificacion <= 20) )
				{
					$notas = $notas + ($calificacion * $creditos);
					
					$total_creditos = $total_creditos + $creditos;
				}
			}

		}

		$cantidad_registro_actas = "";


		$sqlcmd4 = "SELECT count(*) AS cantidad "
				 . "FROM multiactas, record_notas "
				 . "WHERE multiactas.codcohorte='" . $registro->codcohorte . "' AND multiactas.codasig='$codasig' AND "
				 . "record_notas.cedula='$cedula' and multiactas.mid=record_notas.mid ";

		$query4 = mysql_db_query(DB_DATABASE,"$sqlcmd4");

		if ($registro7 = mysql_fetch_object($query4))
		{
			$cantidad_multiactas = $registro7->cantidad;
		}


		if ($cantidad_multiactas > 0)
		{

			$sqlcmd5 = "SELECT record_notas.codacta, record_notas.calificacion "
					 . "FROM multiactas, record_notas "
					 . "WHERE multiactas.codcohorte='" . $registro->codcohorte . "' AND multiactas.codasig='$codasig' AND "
					 . "record_notas.cedula='$cedula' AND multiactas.mid=record_notas.mid "
					 . "ORDER BY record_notas.codacta ";

			$query5 = mysql_db_query(DB_DATABASE,"$sqlcmd5");

			while ($registro8 = mysql_fetch_object($query5))
			{
				$codacta = $registro8->codacta;
				$calificacion = $registro8->calificacion;
				$arreglo_calificiaciones[] = $registro8->calificacion;
				
				if ( ($calificacion >= 1) AND ($calificacion <= 20) )
				{
					$notas = $notas + ($calificacion * $creditos);
					
					$total_creditos = $total_creditos + $creditos;
				}
			}


		}
		
		$cantidad_multiactas = "";

	}
	
	return number_format(($notas/$total_creditos), 2, ',', '');
	
}


function writeToLogFile ($content)
{
	$handle = fopen('/var/www/sace/consultas/archivo.log', "a");

	if ($handle)
	{
		$content = '[' . date ("Y-m-d H:i:s") . '] ' . $content . "\n";

		fwrite($handle, $content);
		fclose($handle); 
	}
}

function inicializarElArchivoDeNotas ()
{
	$handle = fopen('/var/www/sace/consultas/alumnos.csv', "w");
}


function agregarInformacionAlArchivoDeNotas ($content)
{
	$handle = fopen('/var/www/sace/consultas/alumnos.csv', "a");
	
	if ($handle)
	{
		fwrite($handle, $content);
		fclose($handle); 
	}
}


function formatearContenido ($contenido)
{
	$contenido = str_replace ('"', '', $contenido);
#	$contenido = utf8_encode ($contenido);
	
#	$contenido = iconv("ISO-8859-1", "UTF-8", $contenido);
#	$contenido = iconv("UTF-8", "ISO-8859-15", $contenido);

	$contenido = '"' . $contenido . '"';
	
	return $contenido;
}

?>

<?
### generar_status.php

$today = date("g:i:s a");

echo "$today\n";

define("DB_DATABASE","cippsv_ce");
define("DB_USER","cippsv");
define("DB_PASSWORD","2112");
define("DB_SERVER","localhost");

mysql_pconnect(DB_SERVER,DB_USER,DB_PASSWORD);

# Busco a c/u de los alumnos por numero de CI y voy a ver a cuantas cohortes pertenecen



/*
$sqlcmd = "SELECT cedula "
		. "FROM record_notas "
		. "GROUP BY cedula "
		. "ORDER BY cedula "
		. "LIMIT 5 ";
*/

$sqlcmd = "SELECT cedula FROM cedulas_temp LIMIT 100 ";

#echo "$sqlcmd1 \n";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cedula = $registro->cedula;


	$sqlcmd2 = "SELECT registro_actas.codcohorte "
			 . "FROM record_notas, registro_actas  "
			 . "WHERE record_notas.cedula='$cedula' AND record_notas.codacta=registro_actas.codacta "
			 . "GROUP BY codcohorte ";

#echo "$sqlcmd2 \n";

	$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
	
	while ($registro2 = mysql_fetch_object($query2))
	{
		$codcohorte = $registro2->codcohorte;
	


		$sqlcmd3 = "SELECT codsede, codopest, fecha_inicio "
				 . "FROM cohortes "
				 . "WHERE codcohorte='$codcohorte' ";
	
		$query3 = mysql_db_query(DB_DATABASE,"$sqlcmd3");

#echo "$sqlcmd3 \n";

		while ($registro3 = mysql_fetch_object($query3))
		{
			$codsede = $registro3->codsede;
			$codopest = $registro3->codopest;
			$fecha_inicio = $registro3->fecha_inicio;
		}
	


		$sqlcmd4 = "SELECT codasig "
				 . "FROM pensum_estudios "
				 . "WHERE codsede='$codsede' AND codopest='$codopest' ";
		
		$query4 = mysql_db_query(DB_DATABASE,"$sqlcmd4");

#echo "$sqlcmd4 \n";

		while ($registro4 = mysql_fetch_object($query4))
		{
			$codasig = $registro4->codasig;


			$sqlcmd5 = "SELECT record_notas.calificacion "
					 . "FROM record_notas, registro_actas "
					 . "WHERE record_notas.cedula='$cedula' AND record_notas.codacta=registro_actas.codacta AND registro_actas.codasig='$codasig' ";
#echo "$sqlcmd5 \n";

			$query5 = mysql_db_query(DB_DATABASE,"$sqlcmd5");
	
	#echo "$sqlcmd5 <BR>";
	
			while ($registro5 = mysql_fetch_object($query5))
			{
				$calificacion = $registro5->calificacion;
				$contador++;
			}
	

/*
			if (! $calificacion)
			{

				$sqlcmd6 = "SELECT record_notas.calificacion "
						 . "FROM record_notas, multiactas "
						 . "WHERE record_notas.cedula='$cedula' AND record_notas.codacta=multiactas.codacta AND multiactas.codasig='$codasig' "
						 . "AND multiactas.mid=record_notas.mid ";

				$query6 = mysql_db_query(DB_DATABASE,"$sqlcmd6");
				
				while ($registro6 = mysql_fetch_object($query6))
				{
					$calificacion = $registro6->calificacion;
				}
			
			#echo "$sqlcmd6 \n";
			
			}
*/



		}

		if ($contador > 20) $mas_de_20 = "SI";
				
		$contador = '';
				
		echo "cedula: $cedula | ";
		echo "cohorte: $codcohorte | ";
		
		echo "mas_de_20: $mas_de_20 \n";
	
		
		
		$mas_de_20 = '';
		
	}
	

	$cedula = '';
	
}

$today = date("g:i:s a");

echo "$today\n";




			
/*
select record_notas.calificacion from record_notas, multiactas 
where record_notas.cedula='10156954' and record_notas.codacta=multiactas.codacta and multiactas.codasig='OC-021' and multiactas.mid=record_notas.mid;



#select record_notas.calificacion from record_notas, registro_actas 
#where record_notas.cedula='4342574' and record_notas.codacta=registro_actas.codacta and registro_actas.codasig='TCI-013';




# si falta uno, marco un FlAG !!!

### fin del ciclo

### si no hubo flag:

insert into status (cedula, codcohorte, status) VALUES ('', '', '');
*/

?>
<?
##############################################################################
### Fecha, dicese de Libreria / Funcion                                    ###
### (c) 2.001 Utilicese con precaucion, mal uso de estas lineas pueden que ###
### empiece a presentar sintomas como: dolor en la naris por concentracion ###
### de mocos, cojonera, dolor entre las nalgas, pestilencia, flatulencia,  ###
### irritacion en las manos, callos en las manos, ojos vidriosos, etc.     ###
##############################################################################

### Fecha debe tener formato YYYY-MM-DD hh:mm:ss (MySQL Standard DATETIME)

function fecha($fecha, $tipo = 'largo')		# El tipo del Mes (ej: Septiembre o Sep) -> Largo o Corto
{

	$meses = array (	"1" => "Enero",
						"2" => "Febrero",
						"3" => "Marzo",
						"4" => "Abril",
						"5" => "Mayo",
						"6" => "Junio",
						"7" => "Julio",
						"8" => "Agosto",
						"9" => "Septiembre",
						"10" => "Octubre",
						"11" => "Noviembre",
						"12" => "Diciembre"		);

	$meses_peq = array (	"1" => "Ene",
							"2" => "Feb",
							"3" => "Mar",
							"4" => "Abr",
							"5" => "May",
							"6" => "Jun",
							"7" => "Jul",
							"8" => "Ago",
							"9" => "Sep",
							"10" => "Oct",
							"11" => "Nov",
							"12" => "Dic"		);


	list($fecha_variable, $hora_variable) = split (' ', $fecha);
	list($ano, $mes, $dia) = split ('-', $fecha_variable);
	list($hora, $min, $seg) = split (':', $hora_variable);

	$dia = ABS($dia);
	$mes = ABS($mes);

	if ( ( ($hora == '00') AND ($min == '00') AND ($seg == '00') ) OR (! $hora_variable) )
	{

			$hora = '';
			$min = '';
			$seg = '';

			if ($tipo == 'corto') $fecha = "$dia de $meses_peq[$mes]. del $ano";
			if ($tipo == 'largo') $fecha = "$dia de $meses[$mes] del $ano";

	} else {

			if ($hora <= '12')
			{

					if ($hora == '00')
					{

							$hora = "12:$min am";

					} else {
				
							$hora = ABS($hora);
									
							if ($hora == '12')
							{
							
								$hora = "$hora:$min pm";
								
							} else {
							
								$hora = "$hora:$min am";
								
							}
					
					}

			} else {
				
				$hora = ($hora - 12);
				$hora = ABS($hora) . ":$min pm";
			}


			if ($tipo == 'corto') $fecha = "$dia de $meses_peq[$mes]. del $ano ($hora)";
			if ($tipo == 'largo') $fecha = "$dia de $meses[$mes] del $ano ($hora)";

	}

	
	return $fecha;
	
}

?>
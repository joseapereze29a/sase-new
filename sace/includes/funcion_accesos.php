<?
##############################################################################
### Accesos 'htaccess', dicese de Libreria / Funcion                       ###
### (c) 2.003 Utilicese con precaucion, mal uso de estas lineas pueden que ###
### empiece a presentar sintomas como: dolor en la naris por concentracion ###
### de mocos, cojonera, dolor entre las nalgas, pestilencia, flatulencia,  ###
### irritacion en las manos, callos en las manos, ojos vidriosos, etc.     ###
##############################################################################

###
### Esta funcion Agrega o Elimina un 'User' del archivo 'htaccess'
###
### Se debe pasar el Modulo o Seccion, la Accion (agregar o remover) y el User
###

function accesos($modulo, $accion, $user)
{
	GLOBAL $DOCUMENT_ROOT;
	
	### Con el 'modulo', construyo el Path del Archivo a Manipular

	if ($modulo == 'home') $archivo = $_SERVER["DOCUMENT_ROOT"] . 'sace/.htaccess';
	if ($modulo != 'home') $archivo = $_SERVER["DOCUMENT_ROOT"] . 'sace/' . $modulo . '/.htaccess';


	### Leo todo el Archivo a Manipular y lo Meto en un Arreglo

	$contenido = file ("$archivo");

	$cantidad = count($contenido);		### Cantidad de Valores del Arreglo 


	### Si voy a Agregar a el Usuario, ejecuto lo siguiente

	if ($accion == 'agregar')
	{

		for ($i=0; $i<$cantidad; $i++)
		{

			if (!  (ereg ('</Limit>', $contenido[$i]) )	 )
			{
				$texto = $texto . $contenido[$i];
			
			} else {

				$texto = $texto . 'require user ' . $user . "\n";
				$texto = $texto . $contenido[$i];
			}

		}

	}


	### Si voy a Eliminar a el Usuario, ejecuto lo siguiente

	if ($accion == 'eliminar')
	{

		for ($i=0; $i<$cantidad; $i++)
		{

			$expresion_regular = "{^require user "  . $user . "$}";
			
			if (!  (preg_match ($expresion_regular, $contenido[$i]) )	 )
			{
				$texto = $texto . $contenido[$i];
			
			}

		}

	}


	### Abro y Blanqueo el Archivo, para grabar la Informacion 'texto'

	$fp = fopen ("$archivo", "w");

	fwrite ($fp, $texto);
	
	fclose ($fp);



	return;

}

### Fin de la Funcion ########################################################
?>
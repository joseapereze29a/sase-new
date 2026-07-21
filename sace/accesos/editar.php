<?

### Este Script Edita los Usuarios del Sistema, para te tengan Acceso a traves de REALMs
### El Script muestra una Forma, que luego que el Operador la actualize, y la envie de vuelta
### al Servidor, el Script se encarga se verificar los Datos.
###
### Si los Datos estan correctos, los Actualiza en la BD, despues los Actualiza en los REALMs y
### por ultimo en el archivo que mantiene las Claves.


### Los Clasicos Includes

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_accesos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/arreglo_modulos.php");



if ($continuar)
{
	### Valido al el 'User' y al 'pass', y veo a su vez, que si el Operador esta
	### Editando a un 'User' dicho Usuario Debe existir en la BD.

	if ($user == '') $user_no_valido = 1;


	if (! $user_no_valido)	### Verifico si el 'user' es Valido o No
	{
	
		$sqlcmd = "SELECT count(*) AS cantidad_encontrada "
				. "FROM usuarios_sace "
				. "WHERE user='$user' ";
		
		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
		while ($registro = mysql_fetch_object($query))
		{
			$cantidad_encontrada = $registro->cantidad_encontrada;
		}

		if ($cantidad_encontrada == 0) $user_no_existe_db = 1;		### Si el 'User' No Existe en la BD, marco el error

	}



	if ($pass == '') $pass_no_valido = 1;	### Si el 'Pass' es invalidor, marco el error



	### Si no hay Error, Actualizo la Data en la BD y ademas en el REALMs, a traves del funcion 'accesos'
	
	if ( (! $user_no_valido) AND (! $user_no_existe_db) AND (! $pass_no_valido) )
	{
	
		### Actualizo los Datos a la Base de Datos

		$sqlcmd = "UPDATE usuarios_sace SET pass='$pass', usuario='$usuario' WHERE user='$user' ";

		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");


		### Actualizo los Datos en los Realm (htaccess)

		### Primero Elimino de Todos los 'htaccess', a el Usuario 'User'

		$cantidad_modulos = count($modulos);


		for ($i=1; $i<$cantidad_modulos+1; $i++)
		{
			accesos($modulos[$i], "eliminar", $user);
		}


		### Ahora, Agrego el Usuario a todos los que tengan el SI, es decir, a todos los 
		### Modulos o Directorios, donde el Operador halla escogido que el 'User' deba tener Acceso.
		
		for ($i=1; $i<$cantidad_modulos+1; $i++)
		{

			if ($$modulos[$i] == 'SI')
			{
				accesos($modulos[$i], "agregar", $user);
			}

		}


		### Actualizo la Clave, en el archivo de Claves 'passwords'
		### Suponiendo que el htpasswd, esta en: /usr/bin/
		### y que ademas el Archivo de Claves
		### esta en: /var/www/html/sace/.passwords

		$comando = "/usr/bin/htpasswd -b /var/www/html/sace/.passwords $user $pass";
		
		$ejecutadito = shell_exec($comando);
		
		
		header ("Location: index.php");
		exit;

	}


}	
?>
<HTML>
<HEAD>
	<TITLE>Sistema Automatizado de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.3 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

<CENTER>

<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
<TR>
	<TD WIDTH="100%" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
	
		<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
		<TR>
			<TD WIDTH="130" ALIGN="center" VALIGN="middle">
				<A HREF="/sace/"><IMG SRC="/sace/imagenes/logo3.jpg" ALT="" WIDTH="111" HEIGHT="110" BORDER="0"></A>
			</TD><TD WIDTH="470" ALIGN="center" VALIGN="middle" BGCOLOR="#000099">
				<IMG SRC="/sace/imagenes/titulo_sace.jpg" ALT="" WIDTH="400" HEIGHT="35">
			</TD>
		</TR>
		</TABLE>

	</TD>
</TR>
</TABLE>

<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<BR><BR>

<FORM ACTION="editar.php" METHOD="post">

<?

### La variable 'ya_pase', tiene la utilidad de hacer que solo la Primera vez que se ejecute 
### el Script es que se va a leer los Datos de la BD, para que en caso de que ocurran errores,
### estos datos se mantenga en el Entorno, y no se vuelvan a leer de la BD, desactualizandolos.

if (! $ya_pase)
{

	### Hago el Query en la BD y Seleciono la Data del Usuario

	$sqlcmd = "SELECT id, pass, usuario "
			. "FROM usuarios_sace "
			. "WHERE user='$user' ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro = mysql_fetch_object($query))
	{
		$pass = $registro->pass;
		$usuario = $registro->usuario;
	}


	### Ahora reviso cada uno de los Modulos o Directorios, para ver en cuales 
	### el Usuario previamente tenia Acceso a ellos

	$cantidad_modulos = count($modulos);

	### Este GREP me sirve para localizar algo como:
	### 	require user maria			(y para que no fallase en el caso de algo como):
	###		require user mariale
	
	$expresion_regular = "{^require user "  . $user . "$}";

	### Ahora me meto en cada uno de los Modulos o Directorios

	for ($i=1; $i<$cantidad_modulos+1; $i++)
	{

		if ($modulos[$i] == 'home') $archivo = $DOCUMENT_ROOT . 'sace/.htaccess';
		if ($modulos[$i] != 'home') $archivo = $DOCUMENT_ROOT . 'sace/' . $modulos[$i] . '/.htaccess';

		### Leo el Archivo en un Arreglo, para verificar si el Usuario tenia Acceso a ese Modulo

		$contenido = file ("$archivo");
		
		$cantidad = count($contenido);		### Cantidad de Valores del Arreglo 


		for ($j=0; $j<$cantidad; $j++)
		{

				if (	(preg_match ($expresion_regular, $contenido[$j]) )	 )		### Si el GREP se cumple, marco la Variable Variante o
				{																	### Mutante, con el SI, para luego en la Forma, mostrar
					$$modulos[$i] = 'SI';											### que el Usuario SI tenia Acceso a dicho mudulo del Ciclo
				}
	
		}
		
		$archivo = array ();	### Como debo leer varios Archivos 'htaccess' y verificarlos, limpio el Arreglo antes de empezar el Ciclo de nuevo

	}

}
?>
<TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD ALIGN="right" WIDTH="150" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Usuario:</B>
		</FONT>
	</TD>
	<TD ALIGN="left" WIDTH="300" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $user ?>
		</FONT>
	</TD>
	<TD ALIGN="left" WIDTH="250" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Nombre y Apellido</B>
		</FONT>
	</TD>

</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="3">
<TR>
	<TD ALIGN="right" WIDTH="150" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Clave</B>
		</FONT>
	</TD>
	<TD ALIGN="left" WIDTH="300" VALIGN="top">
		<INPUT TYPE="password" NAME="pass" VALUE="<? echo $pass ?>" SIZE="18" MAXLENGTH="16">
	</TD>
	<TD ALIGN="left" WIDTH="250" VALIGN="top">
		<INPUT TYPE="text" NAME="usuario" VALUE="<? echo $usuario ?>" SIZE="25" MAXLENGTH="50">
	</TD>
</TR>
</TABLE>

<BR><BR>

<?
	### Si existen errores, muestro cuales son

	if ( ($user_no_valido) OR ($user_no_existe_db) OR ($pass_no_valido) )
	{
		echo '<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">';
		echo '<TR><TD WIDTH="600" ALIGN="left" VALIGN="top">';
		
		echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FF0000">';
		echo '<B>Se ha encontrado algun Error al tratar de procesar la Informaci&oacute;n</B>';
		echo '</FONT><BR><BR>';

	
		if ($user_no_valido)
		{
			echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
			echo '&bull; Debe ingresar un Usuario v&aacute;lido, favor revisar.';
			echo '</FONT><BR>';
		}
		
		if ($user_no_existe_db)
		{
			echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
			echo '&bull; El Usuario que esta intentando Editar, NO existe en la Base de Datos, favor revisar.';
			echo '</FONT><BR>';
		}
		
		if ($pass_no_valido)
		{
			echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
			echo '&bull; Debe ingresar una Clave v&aacute;lida, favor revisar.';
			echo '</FONT><BR>';
		}

		echo '</TD></TR></TABLE>';
		echo '<BR><BR>';
	}
	
?>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<B>Secciones:</B>
</FONT>

<BR><BR>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<?
		### Armo dinamicamente la forma, utilizando el Arreglo de los Modulos y
		### utilizando las Variables Variantes o Mutantes.

		$cantidad_modulos = count($modulos);

		for ($i=1; $i<$cantidad_modulos+1; $i++)
		{
?>
			<TR BGCOLOR="#FFFFFF">
				<TD ALIGN="left" WIDTH="500" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>  &nbsp; &bull; &nbsp; <? echo $modulos_desc[$i] ?></B>
					</FONT>
				</TD>
				<TD ALIGN="center" WIDTH="100" VALIGN="top">
					<SELECT NAME="<? echo $modulos[$i] ?>">
						<?
							if ($$modulos[$i] == 'SI')
							{
								echo '<OPTION VALUE="SI" SELECTED>SI</OPTION>' . "\n";
								echo '<OPTION VALUE="NO">NO</OPTION>' . "\n";
	
							} else {
	
								echo '<OPTION VALUE="SI">SI</OPTION>' . "\n";
								echo '<OPTION VALUE="NO" SELECTED>NO</OPTION>' . "\n";					
							}
						?>
					</SELECT>
				</TD>
			</TR>
<?
		}
?>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD ALIGN="left" WIDTH="500" VALIGN="top">
		<P> </P>
	</TD>
	<TD ALIGN="center" WIDTH="100" VALIGN="top">
		<INPUT TYPE="submit" NAME="continuar" VALUE="Continuar">

		<INPUT TYPE="hidden" NAME="user" VALUE="<? echo $user ?>">
		<INPUT TYPE="hidden" NAME="ya_pase" VALUE="1">
	</TD>
</TR>
</TABLE>

<BR><BR>


<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
<A HREF="/sace/accesos/">Volver a la P&aacute;gina Anterior</A>
</FONT>

<BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
Ir al <A HREF="/sace/">Home</A>
</FONT>


<BR>

</FORM>

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>

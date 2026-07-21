<?
$modulos = array (			"1" => "home",
							"2" => "consultas",
							"3" => "datos_personales",
							"4" => "ingresos",
							"5" => "ediciones",
							"6" => "reportes",
							"7" => "profesores",
							"8" => "eliminar",
							"9" => "accesos"				);



$modulos_desc = array	(	"1" => 'Home - P&aacute;gina Principal',
							"2" => 'Consulta a trav&eacute;s de CI, Nombre y/o Apellido',
							"3" => 'Ingresar o Editar Datos Personales de un Estudiante',
							"4" => 'Ingresar Cohortes, Actas de Estudios y/o Calificaciones',
							"5" => 'Editar Cohortes, Actas de Estudios y/o Calificaciones',
							"6" => 'Reportes Estadist&iacute;cos',
							"7" => 'Manejo de Profesores',
							"8" => 'Eliminar Cohortes, Actas, y/o Notas ',
							"9" => 'PRIVILEGIOS DE ACCESO'										);
					

###
### Este es el Realm minimo que debe existir en cada Modulo (Directorio)
### Recordar, suministrar (chmod) su respectiva permisologia 'og+rw'
###

### AuthUserFile /var/www/sace/.passwords
### AuthGroupFile /dev/null
### AuthName "cippsv.com.ve/sace"
### AuthType Basic
### 
### <Limit GET POST>
### </Limit>
?>

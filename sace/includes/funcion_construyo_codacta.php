<?
##############################################################################
### Construyo codacta, dicese de Libreria / Funcion                        ###
### (c) 2.003 Utilicese con precaucion, mal uso de estas lineas pueden que ###
### empiece a presentar sintomas como: dolor en la naris por concentracion ###
### de mocos, cojonera, dolor entre las nalgas, pestilencia, flatulencia,  ###
### irritacion en las manos, callos en las manos, ojos vidriosos, etc.     ###
##############################################################################

### Esta funcion crea el "codacta", basado en el "codcohorte" y en el "codasig"
###
### Ejemplos:
###
### codcohorte    | codasig  | codacta
### --------------+----------+----------------
###	COCTCI98-I    | TCI-009  | COCTCI98I-09CD1
###	MATIDEp99-IV  | IDEp-002 | MATIDEp99IV-02
###
###
### Me deben pasar el "codcohorte", el "codasig" y si es CD: el numero del CD.

function construyo_codacta($codcohorte, $codasig, $cd = '0')
{

	list($cohorte, $periodo_lectivo) = split("-", $codcohorte);

	list($letras, $numero) = split("-", $codasig);

	$numero = substr($numero, -2);

	$codacta = $cohorte . $periodo_lectivo . '-' . $numero;

	if ($cd > 0) $codacta = $codacta . 'CD' . $cd;



	return $codacta;
}

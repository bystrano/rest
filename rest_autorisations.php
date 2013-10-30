<?php
/**
 * Définit les autorisations du plugin REST
 *
 * @plugin     REST
 * @copyright  2013
 * @author     Michel Bystranowski
 * @licence    GNU/GPL
 * @package    SPIP\Rest\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier d'autorisations permet de regrouper
 * les fonctions d'autorisations de votre plugin
 */

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function rest_autoriser(){}

/* permissions simples, seuls les admins et redacs ont accès à l'API */
function autoriser_rest_objet($verbe, $type, $id, $qui) {

  return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/* Exemple
function autoriser_configurer_rest_dist($faire, $type, $id, $qui, $opt) {
	// type est un objet (la plupart du temps) ou une chose.
	// autoriser('configurer', '_rest') => $type = 'rest'
	// au choix
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
	return autoriser('configurer', '', $id, $qui, $opt); // seulement les administrateurs complets
	return $qui['statut'] == '0minirezo'; // seulement les administrateurs (même les restreints)
	// ...
}
*/



?>
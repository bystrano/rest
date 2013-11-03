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

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function rest_autoriser(){}

/* permissions simples, seuls les admins et redacs ont accès à l'API */
function autoriser_rest($faire, $type, $id, $qui, $opt) {

  return in_array($qui['statut'], array('0minirezo', '1comite'));
}

function autoriser_rest_objet($verbe, $type, $id, $qui, $opt) {

  return in_array($qui['statut'], array('0minirezo', '1comite'));
}
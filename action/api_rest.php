<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function rest_repondre ($status, $reponse) {

  rest_http_status($status);
  echo json_encode($reponse);

  exit();
}

function action_api_rest_dist () {

  include_spip('inc/headers');

  header('Content-Type: application/json; charset: utf8;');

  $args = explode('/', rtrim(_request('arg'), '/'));
  $ressource = array_shift($args);

  /* Un tableau décrivant les ressources, les paramètres et leurs
     méthodes de validation */
  include_spip('yaml_fonctions');
  $table_des_ressources = pipeline('ressources_rest',
                                   decoder_yaml(
                                       find_in_path('ressources.yaml')));

  if ( ! autoriser('rest')) {
    rest_repondre(403,
                  array('erreur' => array('erreur' => _T('rest:interdit'))));
  }

  if ( ! $ressource) {
    rest_repondre(200,
        array(
          'message' => _T('rest:bienvenue',
                          array('ressources' => implode(', ', array_keys($table_des_ressources))))));
  }

  if ( ! in_array($ressource, array_keys($table_des_ressources))) {
    rest_repondre(404,
        array(
          'erreur' => _T('ressource_inconnue',
                         array('ressource' => $ressource))));
  }

  $verbe  = strtolower($_SERVER['REQUEST_METHOD']);
  $action = charger_fonction($ressource . '_' . $verbe, 'rest', TRUE);

  if ( ! $action) {

    rest_repondre(405,
        array('erreur' => _T('rest:methode_non_supportee',
                             array('methode' => $_SERVER['REQUEST_METHOD']))));
  } else {

    if ( ! autoriser('rest_' . $ressource, $verbe)) {
      rest_repondre(403, array('erreur' => _T('rest:interdit')));
    } else {
      list($status, $reponse) =
        $action($table_des_ressources[$ressource], $args);
      rest_repondre($status, $reponse);
    }
  }

}

/**
 * Fork de la fonction http_status de inc/headers.php
 * Ajoute des status strings.
 */
function rest_http_status($status) {
	global $REDIRECT_STATUS, $flag_sapi_name;
	static $status_string = array(
		200 => '200 OK',
		204 => '204 No Content',
		301 => '301 Moved Permanently',
		302 => '302 Found',
		304 => '304 Not Modified',
        400 => '400 Bad Request',
		401 => '401 Unauthorized',
		403 => '403 Forbidden',
		404 => '404 Not Found',
        405 => '405 Not Allowed',
        500 => '500 Internal Server Error',
		503 => '503 Service Unavailable',
	);

	if ($REDIRECT_STATUS && $REDIRECT_STATUS == $status) return;

	$php_cgi = ($flag_sapi_name AND preg_match(",cgi,i", @php_sapi_name()));
	if ($php_cgi)
		header("Status: ".$status_string[$status]);
	else
		header("HTTP/1.0 ".$status_string[$status]);
}
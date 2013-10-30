<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_rest_dist () {

  include_spip('inc/headers');

  header('Content-Type: application/json; charset: utf8;');

  $liste_ressources = array('objet');

  $ressource = _request('ressource');

  if ( ! in_array($ressource, $liste_ressources)) {
    http_status(404);
    exit;
  }

  $verbe = strtolower($_SERVER['REQUEST_METHOD']);

  if ( ! autoriser('rest_' . $ressource, $verbe)) {
    $status  = 403;
    $reponse = array('erreur' => _T('rest:interdit'));
  } else {
    $action = charger_fonction($ressource . '_' . $verbe, 'rest', TRUE);

    if ( ! $action) {
      $status  = 504;
      $reponse = array(
        'erreur' => _T('rest:methode_non_supportee',
                       array('methode' => $_SERVER['REQUEST_METHOD'])),
      );
    } else {
      list($status, $reponse) = $action();
    }
  }

  rest_http_status($status);
  echo json_encode($reponse);

  exit();
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
		401 => '401 Unauthorized',
		403 => '403 Forbidden',
    400 => '400 Bad Request',
		404 => '404 Not Found',
    500 => '500 Internal Server Error',
		503 => '503 Service Unavailable',
    504 => '504 Not Allowed',
	);

	if ($REDIRECT_STATUS && $REDIRECT_STATUS == $status) return;

	$php_cgi = ($flag_sapi_name AND preg_match(",cgi,i", @php_sapi_name()));
	if ($php_cgi)
		header("Status: ".$status_string[$status]);
	else
		header("HTTP/1.0 ".$status_string[$status]);
}
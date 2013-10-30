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

  /* TODO check permissions */

  $action = charger_fonction(
              $ressource . '_' . strtolower($_SERVER['REQUEST_METHOD']),
              'rest', TRUE);

  if ( ! $action) {
    rest_method_not_allowed(_T('rest:methode_non_supportee',
                               array('methode' => $_SERVER['REQUEST_METHOD'])));
    exit;
  }

  /* $status doit être un des codes supportés par http_status(), à savoir
		200 => '200 OK',
		204 => '204 No Content',
		301 => '301 Moved Permanently',
		302 => '302 Found',
		304 => '304 Not Modified',
		401 => '401 Unauthorized',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		503 => '503 Service Unavailable'
  */
  list($status, $reponse) = $action();

  http_status($status);

  echo json_encode($reponse);

  exit();
}

function rest_method_not_allowed($msg) {

  /* pompé dans inc/headers.php */
	$php_cgi = ($flag_sapi_name AND preg_match(",cgi,i", @php_sapi_name()));
	if ($php_cgi)
		header("Status: 504 Not Allowed");
	else
		header("HTTP/1.0 504 Not Allowed");

  echo json_encode(array('error' => $msg));
  exit();
}
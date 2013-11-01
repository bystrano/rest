<?php

function rest_objet_put_dist ($parametres, $args) {

  include_spip('inc/rest');
  include_spip('rest/objet');

  list($objet, $id_objet) = $args;

  list($status, $reponse) = valider_requete($parametres, $args);

  /* si $status est FALSE, c'est que la validation s'est bien passée */
  if (( ! $status) AND (! autoriser('modifier', $objet, $id_objet))) {
    $status  = 403;
    $reponse = array('erreur' => _T('rest:interdit'));
  }

  if ( ! $status) {
    include_spip('action/editer_objet');

    $table_sql = table_objet_sql($objet);
    $set = _request_champs_table($table_sql);

    if ($err = objet_modifier($objet, $id_objet, $set)) {
      $status  = 500;
      $reponse = array('erreur' => $err);
    } else {
      $status  = 200;
      $reponse = array(
                   'redirect' => calculer_url_rest_objet($objet,
                                                         $id_objet));
    }
  }

  return array($status, $reponse);
}
<?php

function rest_objet_get_dist ($parametres, $args) {

  include_spip('inc/rest');
  include_spip('rest/objet');

  list($objet, $id_objet) = $args;

  list($status, $reponse) = valider_requete($parametres, $args);

  /* si $status est FALSE, c'est que la validation s'est bien passée */
  if (( ! $status) AND ( ! autoriser('voir', $objet, $id_objet))) {
    $status  = 403;
    $reponse = array('erreur' => _T('rest:interdit'));
  }

  if ( ! $status) {
    include_spip('base/abstract_sql');

    $table_sql = table_objet_sql($objet);

    $r = sql_fetsel('*', $table_sql, "id_" . $objet . '=' . intval($id_objet));
    if ( ! $r) {
      $status  = 404;
      $reponse = array('erreur' => _T('rest:objet_non_trouve'));
    } else {
      $status  = 200;
      $reponse = $r;
    }
  }

  return array($status, $reponse);
}
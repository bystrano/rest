<?php

function rest_objet_get_dist () {

  include_spip('rest/objet');

  $objet    = _request('objet');
  $id_objet = _request('id_objet');

  list($status, $reponse) = valider_requete_objet($objet, $id_objet);

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
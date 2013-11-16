<?php

function rest_objet_get_dist ($parametres, $args) {

  include_spip('inc/rest');

  list($objet, $id_objet) = $args;

  if (list($status, $reponse) = valider_requete($parametres, $args)) {
    rest_repondre($status, $reponse);
  }

  /* si $status est FALSE, c'est que la validation s'est bien passée */
  if ( ! autoriser('voir', $objet, $id_objet)) {
    rest_repondre(403, array('erreur' => _T('rest:interdit')));
  }

  include_spip('base/abstract_sql');

  $table_sql = table_objet_sql($objet);

  $r = sql_fetsel('*', $table_sql, "id_" . $objet . '=' . intval($id_objet));
  if ( ! $r) {
    rest_repondre(404, array('erreur' => _T('rest:objet_non_trouve')));
  } else {
    rest_repondre(200, $r);
  }
}
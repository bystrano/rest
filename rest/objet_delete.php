<?php

function rest_objet_delete_dist ($parametres, $args) {

  include_spip('inc/rest');

  list($objet, $id_objet) = $args;

  if (list($status, $reponse) = valider_requete($parametres, $args)) {
    rest_repondre($status, $reponse);
  }

  /* si $status est FALSE, c'est que la validation s'est bien passée */
  if ( ! autoriser('supprimer', $objet, $id_objet)) {
    rest_repondre(403, array('erreur' => _T('rest:interdit')));
  }

  include_spip('base/abstract_sql');
  include_spip('action/editer_liens');

  $table_sql = table_objet_sql($objet);

  $r = sql_delete($table_sql, "id_" . $objet . "=" . intval($id_objet));
  if ( ! $r) {
    rest_repondre(404, array('erreur' => _T('rest:objet_non_trouve')));
  } else {
    objet_dissocier(array($objet => $id_objet), '*');
    pipeline('trig_supprimer_objets_lies',
             array(
                 array('type' => $objet,
                       'id'   => $id_objet)));
    rest_repondre(200, array('ok' => 'ok'));
  }
}
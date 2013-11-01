<?php

function rest_lien_get_dist ($parametres, $args) {

  include_spip('inc/rest');

  list($source, $id_source, $cible, $id_cible) = $args;

  list($status, $reponse) = valider_requete($parametres, $args);

  /* si $status est FALSE, c'est que la validation s'est bien passée */
  if (( ! $status)
      AND ( ! autoriser('voir', $source, $id_source))
      AND ( ! autoriser('voir', $cible,  $id_cible))) {
    $status  = 403;
    $reponse = array('erreur' => _T('rest:interdit'));
  }

  if ( ! $status) {
    include_spip('base/abstract_sql');
    include_spip('action/editer_liens');

    $status = 200;
    list($cle, $table_sql) = objet_associable($source);
    if ( ! $cle) {
      $status  = 403;
      $reponse = array(
                   'erreur' => _T('rest:objet_pas_associable',
                                  array('objet' => $source)));
    } else {
      $r = sql_fetsel('*',
                      $table_sql,
                      array(
                        'id_'.$source . '=' . intval($id_source),
                        'objet=' . sql_quote($cible),
                        'id_objet' . '=' . intval($id_cible),
                     ));
      $status  = 200;
      $reponse = $r;
    }
  }

  return array($status, $reponse);
}
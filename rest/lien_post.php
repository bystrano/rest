<?php

function rest_lien_post_dist ($parametres, $args) {

  include_spip('inc/rest');
  include_spip('rest/lien');

  list($source, $id_source, $cible, $id_cible) = $args;

  list($status, $reponse) = valider_requete($parametres, $args);

  /* si $status est FALSE, c'est que la validation s'est bien passée */
  if (( ! $status)
      AND ( ! autoriser('supprimer', $source, $id_source))
      AND ( ! autoriser('supprimer', $cible,  $id_cible))) {
    $status  = 403;
    $reponse = array('erreur' => _T('rest:interdit'));
  }

  if ( ! $status) {
    include_spip('action/editer_liens');

    $status = 200;
    list($cle, $table_sql) = objet_associable($source);
    if ( ! $cle) {
      $status  = 403;
      $reponse = array(
                   'erreur' => _T('rest:objet_pas_associable',
                                  array('objet' => $source)));
    } else {

      $set = _request_champs_table($table_sql);

      objet_associer(array($source => $id_source),
                     array($cible  => $id_cible),
                     $set);
      $status  = 200;
      $reponse =
        array(
          'redirect' => calculer_url_rest($parametres, $args),
        );
    }
  }

  return array($status, $reponse);
}
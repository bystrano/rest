<?php

function rest_lien_delete_dist ($parametres, $args) {

  include_spip('inc/rest');

  list($source, $id_source, $cible, $id_cible) = $args;

  if (list($status, $reponse) = valider_requete($parametres, $args)) {
    rest_repondre($status, $reponse);
  }

  /* si $status est FALSE, c'est que la validation s'est bien passée */
  if  (( ! autoriser('supprimer', $source, $id_source))
   AND ( ! autoriser('supprimer', $cible,  $id_cible))) {
    rest_repondre(403, array('erreur' => _T('rest:interdit')));
  }

  include_spip('action/editer_liens');

  $status = 200;
  list($cle, $table_sql) = objet_associable($source);
  if ( ! $cle) {
    rest_repondre(403, array('erreur' => _T('rest:objet_pas_associable',
                                            array('objet' => $source))));
  } else {
    objet_dissocier(array($source => $id_source),
                    array($cible  => $id_cible));
    rest_repondre(200, array('ok' => 'ok'));
  }
}
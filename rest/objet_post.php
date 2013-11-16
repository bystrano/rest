<?php

function rest_objet_post_dist ($parametres, $args) {

  include_spip('inc/rest');
  include_spip('rest/objet');

  list($objet, $id_objet) = $args;

  if (list($status, $reponse) = valider_requete($parametres, $args)) {
    rest_repondre($status, $reponse);
  }

  /* si $status est FALSE, c'est que la validation s'est bien passée */
  if ( ! autoriser('creer', $objet, $id_objet)) {
    rest_repondre(403, array('erreur' => _T('rest:interdit')));
  }

  include_spip('action/editer_objet');

  $table_sql = table_objet_sql($objet);
  $set = _request_champs_table($table_sql);

  if ( ! $id_objet) {
    $id_objet = objet_inserer($objet, $set['id_parent']);
    $args[] = $id_objet;
  }

  if ( ! $id_objet) {
    rest_repondre(500,
                  array('erreur' => _T('rest:creation_objet_echoue',
                                       array('objet' => $objet))));
  } else {

    if ($err = objet_modifier($objet, $id_objet, $set)) {
      rest_repondre(500, array('erreur' => $err));
    } else {
      array_unshift($args, 'objet');
      rest_repondre(200, array('redirect' => calculer_url_rest($args)));
    }
  }
}
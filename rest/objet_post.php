<?php

function rest_objet_post_dist () {

  include_spip('rest/objet');

  $objet    = _request('objet');
  $id_objet = 'new';

  list($status, $reponse) = valider_requete_objet();

  /* si $status est FALSE, c'est que la validation s'est bien passée */
  if (( ! $status) AND (! autoriser('creer', $objet, $id_objet))) {
    $status  = 403;
    $reponse = array('erreur' => _T('rest:interdit'));
  }

  if ( ! $status) {
    include_spip('action/editer_objet');

    $set = _request_champs_objet($objet);

    $id_objet = objet_inserer($objet, $set['id_parent']);

    if ( ! $id_objet) {
      $status  = 500;
      $reponse = _T('rest:creation_objet_echoue', array('objet' => $objet));
    } else {

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
  }

  return array($status, $reponse);
}
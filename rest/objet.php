<?php

function valider_requete_objet ($objet, $id_objet) {

  if ( ! $objet) {
    $status  = 400;
    $reponse = array(
      'erreur' => _T('rest:parametre_manquant',
                     array('parametre' => 'objet')),
    );
  } else if ( ! $id_objet) {
    $status  = 400;
    $reponse = array(
      'erreur' => _T('rest:parametre_manquant',
                     array('parametre' => 'id_objet')),
    );
  } else {
    $table_sql = table_objet_sql($objet);
    $trouver_table = charger_fonction('trouver_table','base');
    $desc = $trouver_table($table_sql);
    if (!$desc OR !isset($desc['field'])) {
      $status  = 404;
      $reponse = array(
         'erreur' => _T('rest:objet_inconnu', array('objet' => $objet)),
      );
    } else {
      $status = FALSE;
    }
  }

  return array($status, $reponse);
}

function _request_champs_objet ($objet) {

  $table_sql = table_objet_sql($objet);
  $trouver_table = charger_fonction('trouver_table','base');
  $desc = $trouver_table($table_sql);

  $champs = $desc['field'];

  $set = array();
  foreach (array_keys($champs) as $champ) {
    if ($var = _request($champ))
      $set[$champ] = $var;
  }

  return $set;
}

function calculer_url_rest_objet ($objet, $id_objet) {

  $url = self();
  $url = parametre_url($url, 'objet', $objet);
  $url = parametre_url($url, 'id_objet', $id_objet);

  return url_absolue($url);
}
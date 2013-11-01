<?php

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
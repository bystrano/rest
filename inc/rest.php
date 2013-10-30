<?php

function valider_requete ($tests) {

  foreach ($tests['param_existe'] as $param) {
    if ( ! _request($param)) {
      return array(400,
                   array(
                     'erreur' => _T('rest:parametre_manquant',
                                    array('parametre' => $param))));
    }
  }

  foreach ($tests['objet_existe'] as $param) {
    $table_sql = table_objet_sql(_request($param));
    $trouver_table = charger_fonction('trouver_table','base');
    $desc = $trouver_table($table_sql);
    if (!$desc OR !isset($desc['field'])) {
      return array(404,
                   array(
                     'erreur' => _T('rest:objet_inconnu',
                                    array('objet' => _request($param)))));
    }
  }
}
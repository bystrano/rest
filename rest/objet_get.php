<?php

function rest_objet_get_dist () {

  $objet    = _request('objet');
  $id_objet = _request('id_objet');

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
    include_spip('base/abstract_sql');

    $table_sql = table_objet_sql($objet);
    $trouver_table = charger_fonction('trouver_table','base');
    $desc = $trouver_table($table_sql);
    if (!$desc OR !isset($desc['field'])) {
      $status  = 404;
      $reponse = array(
         'erreur' => _T('rest:objet_inconnu', array('objet' => $objet)),
      );
    } else {
      $r = sql_fetsel('*', $table_sql, "id_" . $objet . '=' . intval($id_objet));
      if ( ! $r) {
        $status  = 404;
        $reponse = array('erreur' => _T('rest:objet_non_trouve'));
      } else {
        $status  = 200;
        $reponse = $r;
      }
    }
  }

  return array($status, $reponse);
}
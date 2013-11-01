<?php

function valider_requete ($parametres, $args) {

  foreach ($parametres as $i => $parametre) {
    foreach ($parametre['criteres'] as $critere) {

      if ($r = call_user_func_array('valider_' . $critere,
                                    array($parametre['nom'], $args[$i]))) {
        return list($status, $reponse) = $r;
      }
    }
  }
}

function valider_obligatoire ($nom_parametre, $valeur) {

  if ($valeur === NULL) {
    return array(400,
                 array(
                   'erreur' => _T('rest:parametre_manquant',
                                  array('parametre' => $nom_parametre))));
  }
}

function valider_objet_existe ($nom, $valeur) {

  $table_sql = table_objet_sql($valeur);
  $trouver_table = charger_fonction('trouver_table','base');
  $desc = $trouver_table($table_sql);
  if (!$desc OR !isset($desc['field'])) {
    return array(404,
                 array(
                   'erreur' => _T('rest:objet_inconnu',
                                  array('objet' => $valeur))));
  }
}

function _request_champs_table ($table_sql) {

  $trouver_table = charger_fonction('trouver_table', 'base');
  $desc = $trouver_table($table_sql);

  $champs = $desc['field'];

  $set = array();
  foreach (array_keys($champs) as $champ) {
    if ($var = _request($champ))
      $set[$champ] = $var;
  }

  return $set;
}

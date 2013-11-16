<?php

function valider_requete ($parametres, $args) {

  $verbe  = strtolower($_SERVER['REQUEST_METHOD']);

  foreach ($parametres[$verbe] as $i => $parametre) {
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

function calculer_url_rest ($args) {

  $base = preg_replace('#rest\.api.*$#', '', url_de_base(0));
  array_unshift($args, 'rest.api');
  array_unshift($args, rtrim($base, '/'));
  $url = implode('/', $args);

  include_spip('inc/filtres_mini');

  return url_absolue($url);
}
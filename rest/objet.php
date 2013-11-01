<?php

function calculer_url_rest_objet ($objet, $id_objet) {

  $url = self();
  $url = parametre_url($url, 'objet', $objet);
  $url = parametre_url($url, 'id_objet', $id_objet);

  return url_absolue($url);
}
#!/usr/bin/python
# -*- coding: utf-8 -*-

import unittest
import requests
from spip_auth import spip_api_path, TestAnonyme, TestRedac, TestWebmestre

class TestLienWebmestre(TestWebmestre):

    def test_lien_get(self):
        '''Un webmestre peut lire les liens'''

        r = self.s.get(spip_api_path('lien/auteur/1/article/1'))
        self.assertEqual(r.status_code, 200)
        self.assertIn('id_auteur', r.json())
        self.assertIn('vu', r.json())
        self.assertIn('id_objet', r.json())
        self.assertIn('objet', r.json())

    def test_lien_post(self):
        '''Un webmestre peut créer, éditer et supprimer des liens.'''

        r = self.s.post(spip_api_path('lien/auteur/2/article/1'))
        self.assertEqual(r.status_code, 200)
        # POST retourne une url vers le lien nouvellement créé
        self.assertIn('redirect', r.json())
        r = self.s.get(r.json()['redirect'])
        # On peut suivre ce lien pour récupérer les données complète du lien
        self.assertEqual(r.status_code, 200)
        self.assertIn('id_auteur', r.json())
        self.assertIn('vu', r.json())
        self.assertIn('id_objet', r.json())
        self.assertIn('objet', r.json())
        # On peut effacer ce lien
        r = self.s.delete(spip_api_path('lien/auteur/2/article/1'))
        self.assertEqual(r.status_code, 200)
        self.assertIn('ok', r.json())
        # Demander le lien effacé ne retourne rien
        r = self.s.get(spip_api_path('lien/auteur/2/article/1'))
        self.assertEqual(r.status_code, 404)
        self.assertIn('erreur', r.json())


class TestLienAnonyme(TestAnonyme):

    def test_lien_get(self):
        '''Les requêtes GET sont interdites aux anonymes'''

        r = self.s.get(spip_api_path('lien/auteur/1/article/1'))
        self.assertEqual(r.status_code, 403)
        self.assertIn('erreur', r.json())

    def test_lien_post(self):
        '''Les requêtes POST sont interdites aux anonymes'''

        article = {
            'titre': 'Un nouvel article',
            'texte': "Je suis un article de test",
            'statut': 'publie'
        }
        r = self.s.post(spip_api_path('lien/auteur/1/article/1'), data=article)
        self.assertEqual(r.status_code, 403)
        self.assertIn('erreur', r.json())

    def test_lien_delete(self):
        '''Les requêtes DELETE sont interdites aux anonymes'''

        r = self.s.delete(spip_api_path('lien/auteur/1/article/1'))
        self.assertEqual(r.status_code, 403)
        self.assertIn('erreur', r.json())


if __name__ == '__main__':
    unittest.main(verbosity=2)

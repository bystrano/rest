#!/usr/bin/python
# -*- coding: utf-8 -*-

import unittest
import requests
from spip_auth import spip_api_path, TestAnonyme, TestRedac, TestWebmestre

class TestObjetWebmestre(TestWebmestre):

    def test_objet_get(self):
        '''Un webmestre peut lire les objets'''
        r = self.s.get(spip_api_path('objet/article/1'))
        self.assertEqual(r.status_code, 200)
        self.assertIn('titre', r.json())
        self.assertIn('texte', r.json())

    def test_objet_post(self):
        '''Un webmestre peut créer, éditer et supprimer des objets.'''

        article = {
            'titre': 'Un nouvel article',
            'texte': "Je suis un article de test",
            'statut': 'publie'
        }
        r = self.s.post(spip_api_path('objet/article'), data=article)
        self.assertEqual(r.status_code, 200)
        # POST retourne un lien vers l'objet nouvellement créé
        self.assertIn('redirect', r.json())
        r = self.s.get(r.json()['redirect'])
        # On peut suivre ce lien pour récupérer les données complète de l'objet
        self.assertEqual(r.status_code, 200)
        self.assertEqual(r.json()['titre'], 'Un nouvel article')
        id_objet = r.json()['id_article']
        # On peut éditer cet objet
        modifs = {
            'titre': 'Un nouveau titre pour cet article'
        }
        r = self.s.post(spip_api_path('objet/article/' + id_objet), data=modifs)
        self.assertEqual(r.status_code, 200)
        self.assertIn('redirect', r.json())
        # le lien de redirection pointe vers le bon objet
        self.assertEqual(r.json()['redirect'], spip_api_path('objet/article/' + id_objet))
        # l'édition a bien eu lieu
        r = self.s.get(r.json()['redirect'])
        self.assertEqual(r.json()['titre'], 'Un nouveau titre pour cet article')
        # On peut effacer cet objet
        r = self.s.delete(spip_api_path('objet/article/' + id_objet))
        self.assertEqual(r.status_code, 200)
        self.assertIn('ok', r.json())
        # Demander l'objet effacé ne retourne rien
        r = self.s.get(spip_api_path('objet/article/' + id_objet))
        self.assertEqual(r.status_code, 404)
        self.assertIn('erreur', r.json())


if __name__ == '__main__':
    unittest.main(verbosity=2)

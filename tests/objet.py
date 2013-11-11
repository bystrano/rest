#!/usr/bin/python
# -*- coding: utf-8 -*-

import requests
from spip_auth import spip_api_path, TestAnonyme, TestRedac, TestWebmestre

class TestObjetWebmestre(TestWebmestre):

    def test_objet_get(self):
        r = self.s.get(spip_api_path('objet/article/1'))
        self.assertEqual(r.status_code, 200)
        self.assertIn('titre', r.json())
        self.assertIn('texte', r.json())

    def test_objet_post(self):
        '''Le Webmestre peut créer des objets.

        On reçoit comme réponse un lien vers l'objet nouvellement créé.
        '''
        article = {
            'titre': 'Un nouvel article',
            'texte': "Je suis un article de test",
            'statut': 'publie'
        }
        r = self.s.post(spip_api_path('objet/article'), data=article)
        self.assertEqual(r.status_code, 200)
        self.assertIn('redirect', r.json())
        r = self.s.get(r.json()['redirect'])
        self.assertEqual(r.status_code, 200)
        self.assertEqual(r.json()['titre'], 'Un nouvel article')

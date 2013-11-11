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
        '''Un webmestre peut créer des objets.

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
        id_objet = r.json()['id_article']
        r = self.s.delete(spip_api_path('objet/article/' + id_objet))
        self.assertEqual(r.status_code, 200)
        self.assertIn('ok', r.json())


if __name__ == '__main__':
    unittest.main(verbosity=2)

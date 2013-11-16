#!/usr/bin/python
# -*- coding: utf-8 -*-

import unittest
import requests
from spip_auth import spip_api_path, TestAnonyme, TestRedac, TestWebmestre

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

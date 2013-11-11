#!/usr/bin/python
# -*- coding: utf-8 -*-

import pdb

import os
import unittest
import requests
from spip_auth import spip_api_path, TestAnonyme, TestRedac, TestWebmestre

class TestAccesApiWebmestre(TestWebmestre):

    def test_dit_bonjour(self):
        r = self.s.get(spip_api_path(''))
        self.assertEqual(r.status_code, 200)
        self.assertIn('message', r.json())

    def test_rejete_ressources_invalides(self):
        r = self.s.get(spip_api_path('ressource-invalide'))
        self.assertEqual(r.status_code, 404)
        self.assertIn('erreur', r.json())

    def test_rejete_verbes_invalides(self):
        r = self.s.options(spip_api_path('lien/auteur/1/article/1'))
        self.assertEqual(r.status_code, 405)
        self.assertIn('erreur', r.json())

# Les rédacs ne sont limités que par les permissions de SPIP
class TestAccesApiRedac(TestRedac):

    def test_dit_bonjour(self):
        r = self.s.get(spip_api_path(''))
        self.assertEqual(r.status_code, 200)
        self.assertIn('message', r.json())

    def test_rejete_ressources_invalides(self):
        r = self.s.get(spip_api_path('ressource-invalide'))
        self.assertEqual(r.status_code, 404)
        self.assertIn('erreur', r.json())

    def test_rejete_verbes_invalides(self):
        r = self.s.options(spip_api_path('lien/auteur/1/article/1'))
        self.assertEqual(r.status_code, 405)
        self.assertIn('erreur', r.json())

# Les requêtes qui n'ont pas d'autorisation pour l'API ne reçoivent que
# des 403
class TestAccesApiAnonyme(TestAnonyme):

    def test_dit_bonjour(self):
        r = self.s.get(spip_api_path(''))
        self.assertEqual(r.status_code, 403)
        self.assertIn('erreur', r.json())

    def test_rejete_ressources_invalides(self):
        r = self.s.get(spip_api_path('ressource-invalide'))
        self.assertEqual(r.status_code, 403)
        self.assertIn('erreur', r.json())

    def test_rejete_verbes_invalides(self):
        r = self.s.options(spip_api_path('lien/auteur/1/article/1'))
        self.assertEqual(r.status_code, 403)
        self.assertIn('erreur', r.json())


# Auto-discover and run tests
path  = os.path.abspath(os.path.dirname(__file__))
suite = unittest.TestLoader().discover(path, '*.py')

unittest.TextTestRunner(verbosity=2).run(suite)

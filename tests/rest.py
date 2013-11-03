#!/usr/bin/python
# -*- coding: utf-8 -*-

import pdb

import unittest
import requests

# Pour fonctionner, ces tests ont besoin que le plugin SPIP "Incarner"
# soit installé. (https://github.com/bystrano/incarner)

# Pour effectuer des requêtes en tant que webmestre, on utilise un
# cookie de session qu'il faut récupérer dans son navigateur en se
# connectant à l'espace privé et en recopiant la valeur ici :
COOKIE_SESSION_WEBMESTRE = '1_14e4628ce2fe4348224baa5d269a03bf'
HOST                     = 'http://localhost/spip-dev/'

class TestWebmestre(unittest.TestCase):

    def setUp(self):
        # initialise la session de webmestre
        self.s = requests.Session()
        r = self.s.get(HOST,
                       params={
                           'action': 'incarner',
                           'login': 'webmestre'
                       },
                       cookies={
                           'spip_session': COOKIE_SESSION_WEBMESTRE
                       })
        self.assertEqual(r.status_code, 204)

class TestRedac(unittest.TestCase):

    def setUp(self):
        # initialise la session de redac
        self.s = requests.Session()
        r = self.s.get(HOST,
                       params={
                           'action': 'incarner',
                           'login': 'test'
                       },
                       cookies={
                           'spip_session': COOKIE_SESSION_WEBMESTRE
                       })
        self.assertEqual(r.status_code, 204)

class TestAnonyme(unittest.TestCase):

    def setUp(self):
        # initialise la session anonyme
        self.s = requests.Session()


class TestAccesApiWebmestre(TestWebmestre):

    def test_dit_bonjour(self):
        r = self.s.get(HOST + 'rest.api/')
        self.assertEqual(r.status_code, 200)
        self.assertIn('message', r.json())

    def test_rejete_ressources_invalides(self):
        r = self.s.get(HOST + 'rest.api/ressource-invalide')
        self.assertEqual(r.status_code, 404)
        self.assertIn('erreur', r.json())

    def test_rejete_verbes_invalides(self):
        r = self.s.options(HOST + 'rest.api/lien/auteur/1/article/1')
        self.assertEqual(r.status_code, 405)
        self.assertIn('erreur', r.json())

# Les rédacs ne sont limités que par les permissions de SPIP
class TestAccesApiRedac(TestRedac):

    def test_dit_bonjour(self):
        r = self.s.get(HOST + 'rest.api/')
        self.assertEqual(r.status_code, 200)
        self.assertIn('message', r.json())

    def test_rejete_ressources_invalides(self):
        r = self.s.get(HOST + 'rest.api/ressource-invalide')
        self.assertEqual(r.status_code, 404)
        self.assertIn('erreur', r.json())

    def test_rejete_verbes_invalides(self):
        r = self.s.options(HOST + 'rest.api/lien/auteur/1/article/1')
        self.assertEqual(r.status_code, 405)
        self.assertIn('erreur', r.json())

# Les requêtes qui n'ont pas d'autorisation pour l'API ne reçoivent que
# des 403
class TestAccesApiAnonyme(TestAnonyme):

    def test_dit_bonjour(self):
        r = self.s.get(HOST + 'rest.api/')
        self.assertEqual(r.status_code, 403)
        self.assertIn('erreur', r.json())

    def test_rejete_ressources_invalides(self):
        r = self.s.get(HOST + 'rest.api/ressource-invalide')
        self.assertEqual(r.status_code, 403)
        self.assertIn('erreur', r.json())

    def test_rejete_verbes_invalides(self):
        r = self.s.options(HOST + 'rest.api/lien/auteur/1/article/1')
        self.assertEqual(r.status_code, 403)
        self.assertIn('erreur', r.json())



# unittest.main()

suite = unittest.TestLoader().loadTestsFromTestCase(TestAccesApiWebmestre)
unittest.TextTestRunner(verbosity=2).run(suite)

suite = unittest.TestLoader().loadTestsFromTestCase(TestAccesApiRedac)
unittest.TextTestRunner(verbosity=2).run(suite)

suite = unittest.TestLoader().loadTestsFromTestCase(TestAccesApiAnonyme)
unittest.TextTestRunner(verbosity=2).run(suite)

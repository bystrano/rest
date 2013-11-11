#!/usr/bin/python
# -*- coding: utf-8 -*-

import pdb

import os
import unittest
import requests

HOST                   = 'http://localhost/spip-dev/'
LOGIN_WEBMESTRE        = 'webmestre'
MOT_DE_PASSE_WEBMESTRE = 'bonjour'

# Pour fonctionner, ces tests ont besoin que le plugin SPIP "Incarner"
# soit installé. (https://github.com/bystrano/incarner)

# Pour effectuer des requêtes en tant que webmestre, il faut avoir un
# cookie de session valide. On l'obtient en se loguant en http, avec
# les login / mot de passe fournis plus haut :
s    =  requests.Session()
url  =  HOST[:7] + LOGIN_WEBMESTRE + ':' + MOT_DE_PASSE_WEBMESTRE
url += '@' + HOST[7:] + 'ecrire'
r = s.get(url)

COOKIE_SESSION_WEBMESTRE = s.cookies['spip_session']

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



# Auto-discover and run tests
path  = os.path.abspath(os.path.dirname(__file__))
suite = unittest.TestLoader().discover(path, '*.py')

unittest.TextTestRunner(verbosity=2).run(suite)

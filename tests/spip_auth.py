#!/usr/bin/python
# -*- coding: utf-8 -*-

import unittest
import requests

HOST  = 'http://localhost/spip-dev/'
LOGIN = 'webmestre' # Doivent être des identifiants
PWD   = 'bonjour'   # de webmestre

# Pour fonctionner, ces tests ont besoin que le plugin SPIP "Incarner"
# soit installé. (https://github.com/bystrano/incarner)

# Pour effectuer des requêtes en tant que webmestre, il faut avoir un
# cookie de session valide. On l'obtient en se loguant en http, avec
# le login / mot de passe fourni plus haut :
s   = requests.Session()
url = HOST[:7] + LOGIN + ':' + PWD + '@' + HOST[7:] + 'ecrire'
r   = s.get(url)

COOKIE_SESSION_WEBMESTRE = s.cookies['spip_session']

def spip_api_path (api_path):
    '''Retourne l'url absolue vers un appel d'API'''

    return HOST + 'rest.api/' + api_path

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

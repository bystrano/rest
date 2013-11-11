#!/usr/bin/python
# -*- coding: utf-8 -*-

import requests
from spip_auth import spip_api_path, TestAnonyme, TestRedac, TestWebmestre

class TestObjetWebmestre(spip_auth.TestWebmestre):

    def test_objet_get(self):
        r = self.s.get(spip_auth.spip_api_path('objet/article/1'))
        self.assertEqual(r.status_code, 200)
        self.assertIn('titre', r.json())
        self.assertIn('texte', r.json())

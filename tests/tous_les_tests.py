#!/usr/bin/python
# -*- coding: utf-8 -*-

# Lance tous les tests du répertoire.

import os
import unittest

if __name__ == '__main__':
    # Auto-discover and run tests
    path  = os.path.abspath(os.path.dirname(__file__))
    suite = unittest.TestLoader().discover(path, '*.py')

    unittest.TextTestRunner(verbosity=2).run(suite)

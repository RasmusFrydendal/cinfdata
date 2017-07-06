
"""Provide the site settings"""

from os import path
from yaml import load


def main():
    """Returns the site settings"""
    sitesettings_path = path.join(path.dirname(__file__), "..",
                                  "sitesettings.yaml")
    with open(sitesettings_path, 'rb') as file_:
        sitesettings = load(file_)
    return sitesettings


SITESETTINGS = main()

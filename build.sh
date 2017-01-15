#!/bin/bash

# Script for building grase-www-portal in CI

dch -v $(git describe |tr '-' '.')~$(lsb_release -cs) -b ""
dpkg-buildpackage -us -uc "$@"

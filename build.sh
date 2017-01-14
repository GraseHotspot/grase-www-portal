#!/bin/bash

# Script for building grase-www-portal in CI

version=$(git describe |tr '-' '.')
echo Building $version
dch -b -v $version "CI Build"
dpkg-buildpackage -us -uc "$@"

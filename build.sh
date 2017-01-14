#!/bin/bash

# Script for building grase-www-portal in CI

version=$(git describe |tr '-' '.')
[[ ! -z $DISTRO ]] && version="$version~$DISTRO"
echo Building $version
dch -b -v $version "CI Build"
dpkg-buildpackage -us -uc "$@"

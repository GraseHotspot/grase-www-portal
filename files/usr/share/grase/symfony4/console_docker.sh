#!/bin/bash
docker-compose run --rm -w /usr/share/grase/symfony4/ webserver ./bin/console "$@"

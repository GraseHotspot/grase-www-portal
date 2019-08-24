docker-compose up -d
docker-compose exec -w /usr/share/grase/symfony4/ webserver /usr/local/bin/composer install
./run_migrations.sh
#docker-compose run node yarn
yarn
# Needed for assets like material design
#docker-compose run node yarn encore dev --watch
yarn encore dev --watch

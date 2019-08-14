docker-compose up -d
sudo docker-compose exec -w /usr/share/grase/symfony4/ webserver /usr/local/bin/composer install
./run_migrations.sh
docker-compose run node yarn
# Needed for assets like material design
docker-compose run node yarn encore dev --watch

docker-compose up -d
docker-compose exec -w /usr/share/grase/symfony4/ webserver /usr/local/bin/composer install
docker-compose exec -w /usr/share/grase/symfony4/ webserver ./bin/console grase:settings-validate
./run_migrations.sh
#docker-compose run node yarn
yarn
# Needed for assets like material design
#docker-compose run node yarn encore dev --watch

# If we are using JS routing in webpack, we'll need this
#bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json

yarn encore dev --watch

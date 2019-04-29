docker-compose up -d
./run_migrations.sh
docker-compose run node yarn
docker-compose run node yarn encore dev --watch

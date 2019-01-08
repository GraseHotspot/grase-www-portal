sudo docker-compose up -d
./run_migrations.sh
sudo docker-compose run node yarn encore dev --watch

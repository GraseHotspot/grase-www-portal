COMPOSER_FILES = files/usr/share/grase/composer.json files/usr/share/grase/composer.lock
BOWER_FILES = files/usr/share/grase/bower.json 
VERSION = $(shell sed -n '/grase-www-portal/s/[^ ]* (//;s/).*//p;q' debian/changelog)

all: bower composer
#files/usr/share/grase/src/includes/constants.php: debian/changelog
	sed -i 's/APPLICATION_VERSION", "[^"]*"/APPLICATION_VERSION", "$(VERSION)"/' files/usr/share/grase/src/includes/constants.php
	chmod 0440 sudo/grase-www-portal

bower:
	mkdir -p ext-libs/bower
	cp $(BOWER_FILES) ext-libs/bower
	cd ext-libs/bower; /usr/local/bin/bower install

composer:
	mkdir -p ext-libs/composer
	cp $(COMPOSER_FILES) ext-libs/composer
	ln -s ../../files/usr/share/grase/www ext-libs/composer/www
	cd ext-libs/composer; /usr/local/bin/composer install
	rm ext-libs/composer/www


clean:
	rm -fr ext-libs

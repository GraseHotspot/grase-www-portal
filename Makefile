COMPOSER_FILES = files/usr/share/grase/composer.json files/usr/share/grase/composer.lock
BOWER_FILES = files/usr/share/grase/bower.json 
VERSION = $(shell sed -n '/grase-www-portal/s/[^ ]* (//;s/).*//p;q' debian/changelog)

all: bower composer
#files/usr/share/grase/www/radmin/includes/constants.inc.php: debian/changelog
	sed -i 's/APPLICATION_VERSION", "[^"]*"/APPLICATION_VERSION", "$(VERSION)"/' files/usr/share/grase/www/radmin/includes/constants.inc.php

bower:
	mkdir -p ext-libs/bower
	cp $(BOWER_FILES) ext-libs/bower
	cd ext-libs/bower; /usr/local/bin/bower install

composer:
	mkdir -p ext-libs/composer
	cp $(COMPOSER_FILES) ext-libs/composer
	cd ext-libs/composer; /usr/local/bin/composer install


clean:
	rm -fr ext-libs

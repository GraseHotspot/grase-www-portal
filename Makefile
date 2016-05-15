COMPOSER_FILES = files/usr/share/grase/composer.json files/usr/share/grase/composer.lock files/usr/share/grase/vendor/
BOWER_FILES = files/usr/share/grase/bower.json
VERSION = $(shell sed -n '/grase-www-portal/s/[^ ]* (//;s/).*//p;q' debian/changelog)

all: bower composer
#files/usr/share/grase/src/includes/constants.php: debian/changelog
	sed -i 's/APPLICATION_VERSION", "[^"]*"/APPLICATION_VERSION", "$(VERSION)"/' files/usr/share/grase/src/includes/constants.php
	chmod 0440 sudo/grase-www-portal

bower:
	mkdir -p ext-libs/bower
	cp $(BOWER_FILES) ext-libs/bower
	cd ext-libs/bower; /usr/local/bin/bower --allow-root install

composer:
	cd files/usr/share/grase/; /usr/local/bin/composer install
	mkdir -p ext-libs/composer
	cp -r $(COMPOSER_FILES) ext-libs/composer/
	rm -fr files/usr/share/grase/vendor

clean:
	rm -fr ext-libs

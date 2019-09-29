COMPOSER_FILES = files/usr/share/grase/symfony4/composer.json files/usr/share/grase/symfony4/composer.lock files/usr/share/grase/symfony4/vendor/
JS_FILES = files/usr/share/grase/symfony4/public/build/
VERSION = $(shell sed -n '/grase-www-portal/s/[^ ]* (//;s/).*//p;q' debian/changelog)

all: js composer
#files/usr/share/grase/src/includes/constants.php: debian/changelog
	sed -i 's/APPLICATION_VERSION", "[^"]*"/APPLICATION_VERSION", "$(VERSION)"/' files/usr/share/grase/src/includes/constants.php
	chmod 0440 sudo/grase-www-portal

# JS doesn't end up in ext-libs due to webpack compiling it all into our own thing
js:
	cd files/usr/share/grase/symfony4; yarn; yarn encore prod
	rm -fr files/usr/share/grase/symfony4/node_modules

composer:
	cd files/usr/share/grase/symfony4; composer install --no-dev
	mkdir -p ext-libs/composer
	cp -r $(COMPOSER_FILES) ext-libs/composer/
	rm -fr files/usr/share/grase/symfony4/vendor

clean:
	rm -fr ext-libs

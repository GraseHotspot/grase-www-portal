COMPOSER_FILES = files/usr/share/grase/composer.json files/usr/share/grase/composer.lock
BOWER_FILES = files/usr/share/grase/bower.json 

all: bower composer

bower:
	mkdir -p ext-libs/bower
	cp $(BOWER_FILES) ext-libs/bower
	cd ext-libs/bower; /usr/local/bin/bower install

composer:
	mkdir -p ext-libs/composer
	cp $(COMPOSER_FILES) ext-libs/composer
	cd ext-libs/composer; /usr/local/bin/composer install

clean:
	rm -r ext-libs

PHP_BIN:=$(shell which php)
CURL_BIN:=$(shell which curl)
BUNDLE_BIN:=$(shell which bundle)
GUARD_BIN:=$(shell which guard)
SINCE:=v0.1
UNTIL:=HEAD

PHPUNIT:=phpunit.phar

all:test

setup: composer.phar phpunit.phar
	$(BUNDLE_BIN)

composer.phar:
	$(PHP_BIN) -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"

phpunit.phar:
	$(CURL_BIN) -SsLO https://phar.phpunit.de/phpunit.phar

install:
	$(PHP_BIN) composer.phar install

test:
	$(PHP_BIN) $(PHPUNIT) --tap --colors ./tests

testrunner:
	$(GUARD_BIN) -i

demo:
	$(PHP_BIN) examples/demo.php

changelog:
	git log --pretty=format:" * %h %s" $(SINCE)..$(UNTIL) -- src tests

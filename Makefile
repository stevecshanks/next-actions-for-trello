VENDOR_BIN := ./vendor/bin

.PHONY: run
run:
	./bin/console server:run

.PHONY: test
test: phpunit behat

.PHONY: phpunit
phpunit:
	$(VENDOR_BIN)/phpunit

.PHONY: behat
behat:
	$(VENDOR_BIN)/behat --format=progress

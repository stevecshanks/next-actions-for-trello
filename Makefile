VENDOR_BIN := ./vendor/bin

.PHONY: test
test: phpunit behat

.PHONY: phpunit
phpunit:
	$(VENDOR_BIN)/phpunit

.PHONY: behat
behat:
	$(VENDOR_BIN)/behat --format=progress

.PHONY: run test unit behat

VENDOR_BIN := ./vendor/bin

run:
	docker-compose up

test: unit behat

unit:
	docker-compose run symfony $(VENDOR_BIN)/phpunit

behat:
	docker-compose run symfony $(VENDOR_BIN)/behat --format=progress

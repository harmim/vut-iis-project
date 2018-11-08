PRODUCTION := 0
DOCKER := 1
FIX := 0

APP_DIR := app
LIBS_DIR := libs
LOG_DIR := log
NODE_DIR = node_modules
TEMP_DIR := temp
VENDOR_DIR := vendor
WWW_DIR := www
NODE_BIN_DIR := $(NODE_DIR)/.bin
CODE_CHECKER_DIR := $(TEMP_DIR)/code-checker
CODING_STANDARD_DIR := $(TEMP_DIR)/coding-standard
PHPSTAN_DIR := $(TEMP_DIR)/phpstan

CODE_CHECKER_VERSION := ^3.0.0
CODING_STANDARD_VERSION := ^2.0.0
PHPSTAN_VERSION := ^0.10.3

PACK := xharmi00

DOCKER_WEB := docker-compose exec web
DOCKER_NODE := docker-compose exec node
ifeq ($(DOCKER), 0)
	DOCKER_WEB :=
	DOCKER_NODE :=
endif


.PHONY: install
install: composer assets


.PHONY: composer
composer: docker-compose-web
ifeq ($(PRODUCTION), 0)
	$(DOCKER_WEB) composer install --no-interaction --no-progress
else
	$(DOCKER_WEB) composer install --no-interaction --no-progress --no-dev
endif


.PHONY: assets
assets: npm bower grunt


.PHONY: npm
npm: docker-compose-node
ifeq ($(PRODUCTION), 0)
	$(DOCKER_NODE) npm install
else
	$(DOCKER_NODE) npm install --no-dev
endif


.PHONY: bower
bower: npm
ifeq ($(PRODUCTION), 0)
	./$(NODE_BIN_DIR)/bower install --allow-root
else
	./$(NODE_BIN_DIR)/bower install --allow-root --production
endif


.PHONY: grunt
grunt: npm
ifeq ($(PRODUCTION), 0)
	./$(NODE_BIN_DIR)/grunt development
else
	./$(NODE_BIN_DIR)/grunt production
endif


.PHONY: code-checker
code-checker: code-checker-install code-checker-run

.PHONY: code-checker-install
code-checker-install: docker-compose-web
ifeq ($(wildcard $(CODE_CHECKER_DIR)/.), )
	$(DOCKER_WEB) composer create-project nette/code-checker $(CODE_CHECKER_DIR) $(CODE_CHECKER_VERSION) \
		--no-interaction --no-progress --no-dev
endif

.PHONY: code-checker-run
code-checker-run: docker-compose-web
ifeq ($(FIX), 0)
	$(DOCKER_WEB) ./$(CODE_CHECKER_DIR)/code-checker --strict-types --eol --ignore $(WWW_DIR)
else
	$(DOCKER_WEB) ./$(CODE_CHECKER_DIR)/code-checker --strict-types --eol --ignore $(WWW_DIR) --fix
endif


.PHONY: coding-standard
coding-standard: coding-standard-install coding-standard-run

.PHONY: coding-standard-install
coding-standard-install: docker-compose-web
ifeq ($(wildcard $(CODING_STANDARD_DIR)/.), )
	$(DOCKER_WEB) composer create-project nette/coding-standard $(CODING_STANDARD_DIR) $(CODING_STANDARD_VERSION) \
		--no-interaction --no-progress --no-dev
endif

.PHONY: coding-standard-run
coding-standard-run: docker-compose-web
ifeq ($(FIX), 0)
	$(DOCKER_WEB) ./$(CODING_STANDARD_DIR)/ecs check \
		$(APP_DIR) $(LIBS_DIR) $(WWW_DIR) --config coding-standard.yml
else
	$(DOCKER_WEB) ./$(CODING_STANDARD_DIR)/ecs check \
		$(APP_DIR) $(LIBS_DIR) $(WWW_DIR) --config coding-standard.yml --fix
endif


.PHONY: phpstan
phpstan: phpstan-install phpstan-run

.PHONY: phpstan-install
phpstan-install: composer docker-compose-web
ifeq ($(wildcard $(PHPSTAN_DIR)/.), )
	$(DOCKER_WEB) composer create-project phpstan/phpstan-shim $(PHPSTAN_DIR) $(PHPSTAN_VERSION) \
		--no-interaction --no-progress --no-dev
endif

.PHONY: phpstan-run
phpstan-run: docker-compose-web
	$(DOCKER_WEB) ./$(PHPSTAN_DIR)/phpstan analyse -c phpstan.neon


.PHONY: clean
clean:
	git clean -xdff $(LOG_DIR) $(NODE_DIR) $(TEMP_DIR) $(VENDOR_DIR) $(WWW_DIR) $(PACK).zip

.PHONY: clean-cache
clean-cache:
	git clean -xdff $(TEMP_DIR)/cache $(TEMP_DIR)/phpstan-cache


.PHONY: pack
pack: clean $(PACK).zip

$(PACK).zip:
	zip -r $@ ./*


.PHONY: docker-compose-web
docker-compose-web:
ifeq ($(DOCKER), 1)
	docker-compose up -d web
endif

.PHONY: docker-compose-node
docker-compose-node:
ifeq ($(DOCKER), 1)
	docker-compose up -d node
endif

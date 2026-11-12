.DEFAULT_GOAL = help

IMAGE ?= collection-app

# Prints its argument in yellow, with a blank line before and after.
MESSAGE = printf '\n\033[33m%s\033[0m\n\n'
PATHS ?=

# Editor configuration checker variables.
EDITORCONFIG_CHECKER_VERSION = v4.0.2
EDITORCONFIG_CHECKER_SHA256 = 44efaef3ed14c6ab52eb87481d78e5e5c204796dccf26e2f4598a61ba17f8003
EDITORCONFIG_CHECKER = bin/editorconfig-checker-$(EDITORCONFIG_CHECKER_VERSION)
# IDE files and local caches.
EDITORCONFIG_EXCLUDE = ^\.idea/|\.iml$$|\.(phpunit\.result|php-cs-fixer)\.cache$$

# Runs a command inside the app container, with this library mounted.
APP = docker run --rm \
	--entrypoint "" \
	--user "$$(id -u):$$(id -g)" \
	--env HOME=/tmp \
	--env COMPOSER_AUTH \
	--volume "$(CURDIR):/app" \
	--workdir /app \
	$(IMAGE)

.PHONY: help
help: ## List the available commands
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-10s\033[0m %s\n", $$1, $$2}'

.PHONY: init
init: image ## Build the library container and install dependencies
	@$(MESSAGE) "Installing dependencies..."
	$(APP) composer install --no-interaction

.PHONY: image
image: ## Build the library image
	@$(MESSAGE) "Building image..."
	docker build --tag $(IMAGE) - < Dockerfile

.PHONY: ci
ci: init test editorconfig ## Run every check inside the library container
	@$(MESSAGE) "TODO: Add coverage, PHPStan and the vulnerability audit."

.PHONY: test
test: ## Run PHPUnit inside the library container, optionally narrowed with PATHS=...
	@$(MESSAGE) "Running tests..."
	$(APP) bin/phpunit $(PATHS)

.PHONY: editorconfig
editorconfig: $(EDITORCONFIG_CHECKER) ## Check files against .editorconfig inside the library container
	@$(MESSAGE) "Checking editor config..."
	$(APP) $(EDITORCONFIG_CHECKER) -exclude '$(EDITORCONFIG_EXCLUDE)'

$(EDITORCONFIG_CHECKER): | image ## Downloads the pinned release and refuses it unless its checksum matches.
	$(APP) sh -c 'set -e; \
		curl -fsSL -o /tmp/editorconfig-checker.tar.gz https://github.com/editorconfig-checker/editorconfig-checker/releases/download/$(EDITORCONFIG_CHECKER_VERSION)/editorconfig-checker-linux-amd64.tar.gz; \
		echo "$(EDITORCONFIG_CHECKER_SHA256)  /tmp/editorconfig-checker.tar.gz" | sha256sum -c -; \
		mkdir -p bin; \
		tar -xzf /tmp/editorconfig-checker.tar.gz -C /tmp editorconfig-checker; \
		mv /tmp/editorconfig-checker $@'

.PHONY: up
up: ## This library starts no service
	@$(MESSAGE) "Collection is a library: it starts no service."

.PHONY: urls
urls: ## This library exposes no URL
	@$(MESSAGE) "Collection is a library: it starts no service, so there are no URLs."

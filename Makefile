.PHONY: help
help: ## Show the help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: bash
bash: ## Start a bash process in a PHP 8.1 Docker container
	docker run --rm -it \
	-e PHP_EXTENSION_XDEBUG=1 \
	-v $(PWD):/usr/src/app/ \
	thecodingmachine/php:8.1-v4-cli \
	bash

install:
	composer update

autoload:
	composer dump-autoload

lint:
	composer exec 'phpcs --standard=PSR2 src tests web'

test:
	composer exec 'phpunit tests'

.PHONY: clear db pint test docker-local docker-prod docker-prod-down docker-prod-logs

clear:
	clear

db:
	clear
	./vendor/bin/sail pint
	./vendor/bin/sail artisan migrate:fresh --seed

pint:
	./vendor/bin/sail pint

test:
	clear
	./vendor/bin/sail pint
	./vendor/bin/sail artisan test

docker-local:
	docker compose -f compose.yaml up

docker-prod:
	docker compose --env-file .env.production -f compose.prod.yaml up -d

docker-prod-down:
	docker compose --env-file .env.production -f compose.prod.yaml down

docker-prod-logs:
	docker compose --env-file .env.production -f compose.prod.yaml logs -f
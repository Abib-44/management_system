SAIL := ./vendor/bin/sail

PROD := docker compose -p gestionale --env-file .env.production -f compose.prod.yaml

.PHONY: dev dev-stop dev-logs db pint test prod prod-stop prod-down prod-logs prod-status

dev:
	$(SAIL) up -d --wait

dev-stop:
	$(SAIL) down

dev-logs:
	$(SAIL) logs -f

db:
	clear
	$(SAIL) pint
	$(SAIL) artisan migrate:fresh
	$(SAIL) artisan app:ensure-storage-bucket
	$(SAIL) artisan db:seed

pint:
	$(SAIL) pint

test:
	clear
	$(SAIL) pint
	$(SAIL) artisan test

prod:
	$(PROD) up -d --build

prod-stop:
	$(PROD) stop

prod-down:
	$(PROD) down

prod-logs:
	$(PROD) logs -f --tail=100

prod-status:
	$(PROD) ps
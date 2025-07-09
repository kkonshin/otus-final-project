build:
	@docker compose build

up:
	@docker compose up -d

down:
	@docker compose down

console:
	@docker compose exec app bash

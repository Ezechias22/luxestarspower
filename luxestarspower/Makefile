.PHONY: install migrate admin backup restore test deploy clean

install:
	composer install
	cp .env.example .env
	@echo "Configuration complete. Edit .env with your credentials."

migrate:
	php migrations/001_initial_schema.php
	@echo "Database migrated successfully"

admin:
	@read -p "Enter admin email: " email; \
	read -p "Enter admin name: " name; \
	php scripts/create_admin.php "$$email" "$$name"

backup:
	./scripts/backup_db.sh

restore:
	@read -p "Enter backup file path: " file; \
	gunzip < $$file | mysql -u root -p luxestarspower

test:
	@echo "Running tests..."
	php -l public/index.php
	php -l app/**/*.php

deploy:
	git pull origin main
	composer install --no-dev --optimize-autoloader
	@echo "Deployment complete"

clean:
	rm -rf storage/logs/*.log
	rm -rf storage/temp/*
	@echo "Cleanup complete"

dev:
	php -S localhost:8000 -t public/

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down

docker-logs:
	docker-compose logs -f

help:
	@echo "Available commands:"
	@echo "  make install     - Install dependencies"
	@echo "  make migrate     - Run database migrations"
	@echo "  make admin       - Create admin user"
	@echo "  make backup      - Backup database"
	@echo "  make restore     - Restore database"
	@echo "  make test        - Run tests"
	@echo "  make deploy      - Deploy to production"
	@echo "  make clean       - Clean temporary files"
	@echo "  make dev         - Start development server"
	@echo "  make docker-up   - Start Docker containers"
	@echo "  make docker-down - Stop Docker containers"

# WR506D - Movies & Actors API

Application Symfony avec API Platform pour la gestion de films et d'acteurs.

## Fonctionnalités

- API REST avec API Platform
- Authentification JWT
- Authentification par clé API
- Authentification à deux facteurs (2FA)
- Rate limiting
- Upload de médias
- Sérialisation avec groupes personnalisés

## Installation

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

## Configuration

Copier `.env` vers `.env.local` et configurer les variables d'environnement.

## Tests

```bash
vendor/bin/phpunit
vendor/bin/phpcs --standard=PSR2 src/
vendor/bin/phpstan analyze src/ --level=2
vendor/bin/phpmd src/ text cleancode,codesize,controversial,design
```

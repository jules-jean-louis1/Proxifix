# Configuration Docker Centralisée

## Vue d'ensemble

Toute la configuration est maintenant centralisée dans le fichier `docker/.env` au lieu d'utiliser les fichiers `.env` du backend. Cela simplifie la gestion et évite les duplications.

## Structure

```
docker/
├── .env                          # Configuration centralisée
├── docker-compose.yaml          # Utilise les variables du .env
├── scripts/
│   └── setup-database.sh        # Script d'installation des BDD
└── php/
    └── docker-entrypoint.sh     # Point d'entrée du conteneur PHP
```

## Variables d'environnement

### Base de données de développement
- `DB_DEV_NAME=proxifix`
- `DB_DEV_USER=postgres`
- `DB_DEV_PASSWORD=password`
- `DB_DEV_HOST=database`
- `DB_DEV_PORT=5434`

### Base de données de test
- `DB_TEST_NAME=cda_app_test`
- `DB_TEST_USER=cda_app`
- `DB_TEST_PASSWORD=password`
- `DB_TEST_HOST=database_test`
- `DB_TEST_PORT=5433`

### Configuration Symfony
- `APP_ENV=dev`
- `KERNEL_CLASS=App\\Kernel`
- `APP_SECRET=$ecretf0rt3st`
- `SYMFONY_DEPRECATIONS_HELPER=999999`
- `PANTHER_APP_ENV=panther`
- `PANTHER_ERROR_SCREENSHOT_DIR=./var/error-screenshots`

### Configuration JWT
- `JWT_PASSPHRASE=proxifix_jwt_passphrase`
- `JWT_SECRET_KEY=/var/www/symfony/config/jwt/private.pem`
- `JWT_PUBLIC_KEY=/var/www/symfony/config/jwt/public.pem`

## Utilisation

### Démarrer l'environnement
```bash
cd docker
./docker/docker.sh up -d
```

### Voir les logs de configuration
```bash
./docker/docker.sh logs php
```

### Exécuter des commandes Symfony
```bash
# Dans le conteneur PHP
./docker/docker.sh exec php bash

# Migrations
./docker/docker.sh exec php php bin/console doctrine:migrations:migrate

# Tests
./docker/docker.sh exec php php bin/phpunit

# Fixtures
./docker/docker.sh exec php php bin/console doctrine:fixtures:load
```

### Accéder aux bases de données

#### Base de développement
```bash
# Depuis l'host
psql -h localhost -p 5434 -U postgres -d proxifix

# URL de connexion
postgresql://postgres:password@localhost:5434/proxifix
```

#### Base de test
```bash
# Depuis l'host
psql -h localhost -p 5433 -U cda_app -d cda_app_test

# URL de connexion
postgresql://cda_app:password@localhost:5433/cda_app_test
```

## Script automatisé


### `docker-entrypoint.sh`
- Configure les clés JWT
- Installe les dépendances Composer
- Lance le script de configuration des BDD
- Affiche un résumé de la configuration

## Dépannage

### Problèmes de connexion à la BDD
```bash
# Vérifier que les conteneurs sont démarrés
./docker/docker.sh ps

# Voir les logs des bases de données
./docker/docker.sh logs database
./docker/docker.sh logs database_test

# Redémarrer les services
./docker/docker.sh down && ./docker/docker.sh up -d
```

### Regénérer les clés JWT
```bash
# Supprimer les clés existantes
./docker/docker.sh exec php rm -f config/jwt/*.pem

# Redémarrer le conteneur PHP
./docker/docker.sh restart php
```

### Reset complet
```bash
# Arrêter et supprimer tout
./docker/docker.sh down -v

# Redémarrer
./docker/docker.sh up -d
```

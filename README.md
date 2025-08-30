<p align="center">
  <span>
    <img src="docs/logo-proxifix.svg" alt="Proxifix Logo" width="40" height="57" style="vertical-align:middle;"/>
    <span style="font-size:2em;vertical-align:middle;"><b>Proxifix</b></span>
  </span>
</p>

# Gestion d'interventions d'équipements informatiques

Application complète de gestion d'interventions techniques pour les entreprises. Permet de gérer les clients, équipements, techniciens et interventions avec un workflow complet.

## 🚀 Démarrage Rapide avec Docker

### Prérequis

- Docker et Docker Compose installés
- Port 81 (Nginx) et 5432 (PostgreSQL) disponibles

### Installation

```bash
# 1. Cloner le projet
git clone [url-du-repo]
cd proxifix_dev

# 2. Configurer l'environnement Docker
cp docker/.env.tpl docker/.env
# Modifier docker/.env si nécessaire (ports, mots de passe, etc.)

# 3. Démarrer les services
./docker/docker.sh up -d

# 4. Installer les dépendances du backend
./docker/docker.sh exec php composer install

# 5. Configurer la base de données
./docker/docker.sh exec php php bin/console doctrine:database:create --if-not-exists
./docker/docker.sh exec php php bin/console doctrine:migrations:migrate --no-interaction

# 6. Charger les données de test (optionnel)
./docker/docker.sh exec php php bin/console doctrine:fixtures:load --no-interaction

# 7. Installer les dépendances du mobile
cd mobile && npm install
```

### Services Docker

Le projet utilise Docker Compose avec les services suivants :

- **Nginx** (Port 81) - Serveur web
- **PHP-FPM** - Backend Symfony
- **PostgreSQL** (Port 5432) - Base de données
- **Traefik** - Reverse proxy (si activé)

### URLs après installation

- **API Backend** : http://localhost:81
- **Documentation API** : http://localhost:81/api/docs
- **PgAdmin** (si activé) : http://localhost:5050
- **Mobile App** : `cd mobile && npx expo start`

## 🐳 Commandes Docker Utiles

### Gestion des services

```bash
# Démarrer tous les services
./docker/docker.sh up -d

# Arrêter tous les services
./docker/docker.sh down

# Voir les logs
./docker/docker.sh logs -f

# Redémarrer un service spécifique
./docker/docker.sh restart php
```

### Commandes Symfony dans Docker

```bash
# Accéder au conteneur PHP
./docker/docker.sh exec php bash

# Migrations
./docker/docker.sh exec php php bin/console doctrine:migrations:migrate

# Fixtures (données de test)
./docker/docker.sh exec php php bin/console doctrine:fixtures:load

# Tests
./docker/docker.sh exec php php bin/phpunit

# Cache
./docker/docker.sh exec php php bin/console cache:clear
```

### Base de données

```bash
# Accéder à PostgreSQL
./docker/docker.sh exec db psql -U app -d cda_app

# Dump de la base
./docker/docker.sh exec db pg_dump -U app cda_app > backup.sql

# Restaurer une base
./docker/docker.sh exec -i db psql -U app -d cda_app < backup.sql
```

## 📋 Configuration Avancée

### Variables d'environnement (docker/.env)

```bash
# Ports
NGINX_PORT=81
DATABASE_PORT=5432

# Base de données
POSTGRES_DB=cda_app
POSTGRES_USER=app
POSTGRES_PASSWORD=password

# Projet
PROJECT_NAME=proxifix-dev
```

### SSL/HTTPS (Production)

Pour activer HTTPS en production, modifiez le fichier `docker/traefik.yml` et configurez vos certificats SSL.

## 🔧 Développement sans Docker

Si vous préférez développer sans Docker :

```bash
# Backend
cd backend
composer install
symfony server:start

# Mobile
cd mobile
npm install
npx expo start
```

## 📚 Documentation

- 👉 [Guide de Développement &amp; Utilisation avancée](./docs/developpment.md)
- **[🧪 Tests](./docs/test.md)** - Guide des tests unitaires et d'intégration
- **[🧠 Logique Métier](./docs/business-logic.md)** - Règles de gestion et workflows
- **[🔌 API Documentation](./docs/api-documentation.md)** - Documentation complète des endpoints

## 🛠️ Stack Technique

### Backend (API)

- **PHP 8.1+** avec **Symfony 6**
- **PostgreSQL** pour la base de données
- **JWT Authentication** (Lexik Bundle)
- **API Platform** pour les APIs REST
- **PHPUnit** pour les tests

### Mobile (Frontend)

- **React Native** avec **Expo**
- **TypeScript**
- **React Hook Form** pour les formulaires
- **AsyncStorage** pour le cache local

## 📋 Fonctionnalités

### ✅ Gestion des Utilisateurs

- Authentification JWT multi-rôles
- ROLE_CUSTOMER / ROLE_TECHNICIAN / ROLE_ADMIN / ROLE_SUPER_ADMIN
- Gestion des entreprises et permissions

### ✅ Gestion des Interventions

- Création et assignation d'interventions
- Workflow complet : `pending` → `assigned` → `in_progress` → `completed`
- Gestion des équipements et types d'intervention
- Système de tâches associées aux interventions

### ✅ Planification

- Demandes de rendez-vous (AppointmentRequest)
- Gestion des créneaux libres
- Assignation automatique ou manuelle des techniciens

### 🚧 En Développement

- [ ] Application mobile complète
- [ ] Notifications push
- [ ] Rapports et statistiques
- [ ] Module de facturation

## 🏃‍♂️ Commandes Utiles

### Backend

```bash
cd backend

# Démarrer le serveur de développement
symfony server:start

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Lancer les tests
php bin/phpunit

# Charger des données de test
php bin/console doctrine:fixtures:load
```

### Mobile

```bash
cd mobile

# Démarrer l'app en développement
npx expo start

# Build pour production
npx expo build:android
npx expo build:ios
```

## 🌐 URLs de Développement

- **API Backend** : http://localhost:81
- **Documentation API** : http://localhost:81/api/docs
- **Mobile App** : `cd mobile && npx expo start` puis http://localhost:19006 (web)
- **Base de données** : localhost:5432 (PostgreSQL)

## 🔐 Comptes de Test

Après avoir chargé les fixtures avec `./docker/docker.sh exec php php bin/console doctrine:fixtures:load` :

```
Super Admin:
- Email: superadmin@mobile-cda.com
- Password: admin123

Admin Entreprise:
- Email: admin@entreprise.com
- Password: admin123

Customer:
- Email: customer@test.com
- Password: customer123

Technicien:
- Email: tech@entreprise.com
- Password: tech123
```

## 🚨 Dépannage

### Problèmes courants

```bash
# Ports déjà utilisés
sudo lsof -i :81  # Vérifier qui utilise le port 81
sudo lsof -i :5432  # Vérifier qui utilise le port 5432

# Problème de permissions
./docker/docker.sh exec php chown -R www-data:www-data /var/www/symfony/var

# Réinitialiser complètement
./docker/docker.sh down -v  # Supprimer les volumes
./docker/docker.sh up -d    # Redémarrer

# Voir les logs détaillés
./docker/docker.sh logs php  # Logs PHP
./docker/docker.sh logs db   # Logs PostgreSQL
```

**Made with ❤️ by Jules**

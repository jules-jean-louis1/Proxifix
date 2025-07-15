# 🛠️ Gestion d'Interventions Techniques

Application complète de gestion d'interventions techniques pour les entreprises. Permet de gérer les clients, équipements, techniciens et interventions avec un workflow complet.

## 🚀 Démarrage Rapide

```bash
# Cloner le projet
git clone [url-du-repo]
cd mobile-cda

# Démarrer avec Docker
docker compose up -d

# Ou installation manuelle (voir documentation)
cd backend && composer install
cd ../mobile && npm install
```

## 📚 Documentation

### 🔧 Développement
- **[📖 Guide d'Installation](./docs/developpment.md)** - Setup complet du projet
- **[🧪 Tests](./docs/test.md)** - Guide des tests unitaires et d'intégration

### 🏗️ Architecture
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

- **API Backend** : http://127.0.0.1:8000
- **Documentation API** : http://127.0.0.1:8000/api/docs
- **Mobile App** : http://localhost:19006 (web) ou via Expo Go

## 🔐 Comptes de Test

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

## 🤝 Contribution

1. **Fork** le projet
2. Créer une **branch feature** (`git checkout -b feature/nouvelle-fonctionnalite`)
3. **Commit** les changes (`git commit -am 'Ajouter nouvelle fonctionnalité'`)
4. **Push** vers la branch (`git push origin feature/nouvelle-fonctionnalite`)
5. Créer une **Pull Request**

## 📝 Structure du Projet

```
mobile-cda/
├── backend/           # API Symfony
│   ├── src/
│   ├── config/
│   ├── migrations/
│   └── tests/
├── mobile/            # App React Native
│   ├── app/
│   ├── components/
│   └── assets/
├── docs/              # Documentation
│   ├── business-logic.md
│   ├── api-documentation.md
│   ├── developpment.md
│   └── test.md
└── README.md
```

## 📄 Licence

Ce projet est sous licence [MIT](LICENSE).

---

**Made with ❤️ by Jules**

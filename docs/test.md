# Cahier de recette - Backend


- [1. Executer les tests](#1-executer-les-tests)
- [2. Tests Api – Company](#2-tests-api--company)

# 1. Exécuter les tests
Pour exécuter les tests, utilisez la commande suivante dans le terminal :

```bash
php bin/phpunit
```
Ou bien pour cibler un test spécifique, utilisez :

```bash
php bin/phpunit --filter CompanyControllerTest
```

Creer la base de données de test si elle n'existe pas :
```bash
php bin/console doctrine:database:create --env=test
```

Si elle existe déjà, vous pouvez la supprimer et la recréer pour un état propre :

```bash
php bin/console doctrine:database:drop --force --env=test
php bin/console doctrine:database:create --env=test
```

Ensuite, pour mettre à jour le schéma de la base de données et charger les fixtures, exécutez :
```bash
php bin/console doctrine:migrations:migrate --force --env=test
```
```bash
php bin/console doctrine:fixtures:load --env=test
```

# 2. Tests API – Company

- [1. Création d’une company](#1-création-dune-company-post-apicompany)
- [2. Gestion des erreurs](#2-gestion-des-erreurs)
- [3. Lecture (GET)](#3-lecture-get)
- [4. Mise à jour (PUT / PATCH)](#4-mise-à-jour-put--patch)
- [5. Suppression](#5-suppression-delete)
- [6. Extensions à venir](#6-extensions-à-venir-interventions-equipments-appointmentrequest)

---

## 1. Création d’une company (POST /api/company)

- ✅ Vérifie la création complète d’une entreprise avec :
  - Champs obligatoires : `name`, `type`, `address`, `city`, `zip_code`, etc.
  - Champs optionnels : `about`, `website`, `logo`, `open_days`, `open_hours`
  - Ajout de spécialisations via `slug`, `id`, ou `{slug}`, `{id}`
  - Ajout de tâches avec `name` et `description`
  - Ajout de `type_equipments` et `type_interventions` avec création automatique
  - Ajout d’utilisateurs avec rôles, sauf `ROLE_CUSTOMER`
- 🔄 La réponse doit être `201` avec les champs de l’entreprise (groupes `company:read`)

---

## 2. Gestion des erreurs

- ❌ Teste les erreurs si :
  - Le champ `type` est invalide ou inexistant
  - Le champ `name` est manquant
  - L’un des utilisateurs à ajouter n’existe pas ou est `ROLE_CUSTOMER`
  - Une spécialisation est invalide ou inexistante
- 🔄 Les réponses doivent renvoyer les bons codes (`400`, `422`, etc.)

---

## 3. Lecture (GET)

- ✅ `GET /api/company`
  - Retourne la **liste paginée** de toutes les entreprises

- ✅ `GET /api/company/{id}`
  - Retourne les **détails complets** d’une entreprise

---

## 4. Mise à jour (PUT / PATCH)

- 🔄 Met à jour :
  - Les champs simples comme `name`, `about`, etc.
  - Les spécialisations, tâches, équipements et types d’intervention liés
- ✅ Vérifie que les changements sont bien persistés

---

## 5. Suppression (DELETE)

- ✅ `DELETE /api/company/{id}`
  - Supprime l’entreprise correspondante
  - Code retour attendu : `200`
  - Vérifie que l’entreprise n’existe plus ensuite

---

# ✅ Tests complets pour Customer :
🔐 Tests d'Authentification :
testRegisterCustomer : Création d'un nouveau customer avec des données valides
testRegisterCustomerWithExistingEmail : Validation que l'email doit être unique
testRegisterCustomerWithMissingFields : Validation des champs requis
testLogin : Connexion avec un customer existant
testLoginWithInvalidCredentials : Rejet des mauvais identifiants
📊 Tests Fonctionnels :
testGetEquipmentList : Récupération de la liste des équipements
testCustomerCannotJoinCompany : Validation que les customers ne peuvent pas rejoindre une compagnie (test skippé car endpoint non disponible)
🎯 Couverture des Tests :
✅ Validation des données : Champs obligatoires, formats valides
✅ Sécurité : Authentification, autorisation, unicité des emails
✅ Endpoints disponibles : Seulement les endpoints qui existent vraiment
✅ Codes de réponse HTTP : 200, 201, 400, 401, 409
✅ Structure des réponses JSON : Validation des clés attendues
🚀 Tests supplémentaires que vous pourriez ajouter :
Validation d'email : Format email invalide
Mots de passe : Longueur minimale, caractères spéciaux
Pagination : Tests avec paramètres page/size pour les listes
Filtres : Tests de recherche avec paramètres
Tokens JWT : Expiration, format invalide
Limites de taux : Protection contre le spam

## 6. Extensions à venir (Interventions, Equipments, AppointmentRequest)

> Ces sections sont prévues dans les prochains tests :

- 📦 `InterventionControllerTest`
- ⚙️ `EquipmentControllerTest`
- 📆 `AppointmentRequestControllerTest`

---

🧪 Tous les tests sont écrits dans `CompanyControllerTest` sous le namespace :

```php
namespace App\Tests\Controller;
```
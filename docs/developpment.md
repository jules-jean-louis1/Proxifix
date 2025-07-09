# Développement de l'application

## Installation et Configuration

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- PostgreSQL
- Node.js et npm (pour le frontend)

### Installation du Backend

Si vous utilisez docker, vous pouvez démarrer le projet avec la commande suivante :

```bash
docker compose up -d
```
Si vous n'utilisez pas Docker, suivez ces étapes :
1. Clonez le dépôt :
   ```bash
   git clone
   ```
2. Accédez au répertoire du projet :
   ```bash
   cd proaxive_dev/backend
   ```
3. Installez les dépendances PHP :
   ```bash
   composer install
   ```
4. Configurez votre fichier `.env` :
   ```bash
   cp .env .env.local
   ```
   Modifiez les variables d'environnement selon votre configuration (base de données, clés API, etc.).
5. Créez la base de données :
   ```bash
   php bin/console doctrine:database:create
   ```
6. Exécutez les migrations :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
7. Chargez les fixtures (facultatif, pour peupler la base de données avec des données de test) :
   ```bash
   php bin/console doctrine:fixtures:load
   ```
8. Démarrez le serveur Symfony :
   ```bash
   symfony server:start
    ```
### Exécution des tests
Pour exécuter les tests, utilisez la commande suivante :

Configurez votre fichier `.env.test` pour les tests unitaires :
   ```bash
   cp .env.example .env.test
   ```
Modifiez les variables d'environnement selon votre configuration de test (base de données, clés API, etc.).

Assurez-vous que les dépendances de test sont installées :

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
php bin/console doctrine:fixtures:load --env=test
```

```bash
php bin/phpunit
```


# Documentation du Modèle

### MCD (Modèle Conceptuel des Données)

```mermaid
erDiagram
    user {
        int id PK
        string email
        json roles
        string password
        string first_name
        string last_name
        datetime created_at
        datetime updated_at
        string phone
    }

    company {
        int id PK
        string name
        string address
        string city
        string zip_code
        string website
    }

    equipment {
        int id PK
        int user_id FK
        int brand_id FK
        int type_equipment_id FK
        int operating_system_id FK
        string name
    }

    appointment_request {
        int id PK
        int user_id FK
        int company_id FK
        int equipment_id FK
        int type_intervention_id FK
        datetime date
        string status
        int approved_by_id FK
    }

    intervention {
        int id PK
        int company_id FK
        int user_id FK
        int type_intervention_id FK
        int status_id FK
        int equipment_id FK
        int appointment_request_id FK
        string title
        datetime created_at
        datetime updated_at
    }

    task_intervention {
        int id PK
        int task_id FK
        int intervention_id FK
    }

    task {
        int id PK
        string name
        float price
    }

    brand {
        int id PK
        string name
    }

    operating_system {
        int id PK
        string name
    }

    type_equipment {
        int id PK
        string name
    }

    type_intervention {
        int id PK
        string name
    }

    status {
        int id PK
        string name
    }

    refresh_token {
        int id PK
        string refresh_token
        string username
        datetime valid
    }
    user ||--o{ equipment : owns
    user ||--o{ appointment_request : makes
    user ||--o{ intervention : requests
    user ||--o{ refresh_token : has
    user ||--o{ company : works_for
    user ||--o{ appointment_request : approves
    company ||--o{ user : employs
    company ||--o{ intervention : manages
    company ||--o{ appointment_request : receives
    equipment ||--|| type_equipment : is_of_type
    equipment ||--|| brand : is_from
    equipment ||--|| operating_system : runs
    equipment ||--o{ intervention : used_in
    equipment ||--o{ appointment_request : concerns
    appointment_request ||--|| type_intervention : is_of_type
    intervention ||--|| type_intervention : is_of_type
    intervention ||--|| status : has_status
    intervention ||--o{ task_intervention : contains
    task_intervention ||--|| task : defines
    intervention ||--|| appointment_request : from
```

### Diagramme de séquence

Prise de rendez-vous client :

```mermaid
sequenceDiagram
    participant Utilisateur
    participant AppMobile
    participant Serveur
    participant BDD

    Utilisateur->>AppMobile: Sélectionne une entreprise
    AppMobile->>Serveur: Requête: créneaux disponibles
    Serveur->>BDD: SELECT * FROM get_free_slots('date', 'company_id');
    BDD-->>Serveur: Liste créneaux
    Serveur-->>AppMobile: Donne liste créneaux
    AppMobile->>Utilisateur: Affiche les créneaux

    Utilisateur->>AppMobile: Choisit un créneau
    AppMobile->>Serveur: POST /api/appointment_request (créneau sélectionné)
    Serveur->>BDD: INSERT INTO appointment_request (...)
    BDD-->>Serveur: OK
    Serveur-->>AppMobile: Confirmation
    AppMobile-->>Utilisateur: RDV confirmé

```

Validation d’intervention par un technicien :

```mermaid
sequenceDiagram
    participant Tech as Technicien
    participant Frontend
    participant API as InterventionController
    participant DB as Base de données

    Tech->>Frontend: Connexion au dashboard
    Frontend->>API: GET /interventions/{id}
    API->>DB: Récupère intervention
    DB-->>API: Intervention
    API-->>Frontend: Détails intervention

    Tech->>Frontend: Clique sur “Valider l’intervention”
    Frontend->>API: PATCH /interventions/{id}/validate
    API->>DB: MAJ status = "Complété(e)"
    DB-->>API: OK
    API-->>Frontend: Intervention validée
```

Édition d’une entreprise par un admin :

```mermaid
sequenceDiagram
    participant Admin
    participant Frontend
    participant API as CompanyController
    participant DB as Base de données

    Admin->>Frontend: Accès à la fiche entreprise
    Frontend->>API: GET /companies/{id}
    API->>DB: SELECT * FROM company WHERE id = ?
    DB-->>API: Données entreprise
    API-->>Frontend: Détails entreprise

    Admin->>Frontend: Modifie infos (nom, adresse, etc.)
    Frontend->>API: PUT /companies/{id}
    API->>DB: UPDATE company SET ...
    DB-->>API: OK
    API-->>Frontend: Confirmation
```

Création d’un équipement (avec type, marque, OS, notes) :

```mermaid
sequenceDiagram
    participant User
    participant Frontend
    participant API as EquipmentController
    participant DB as Base de données

    User->>Frontend: Clique sur “Ajouter un équipement”
    <!-- Frontend->>API: GET /brands, /os, /types -->
    API->>DB: Récupère marques, OS, types
    DB-->>API: Données
    API-->>Frontend: Liste déroulante

    User->>Frontend: Remplit le formulaire (nom, marque, type, OS, note)
    Frontend->>API: POST /equipments
    API->>DB: INSERT INTO equipment (...)
    DB-->>API: OK
    API-->>Frontend: Équipement créé
```

Demande d'une creation d'entreprise par un utilisateur :

| `is_approved` | Signification                      |
| ------------- | ---------------------------------- |
| `null`        | En attente de validation (pending) |
| `true`        | Acceptée par un `super_admin`      |
| `false`       | Refusée par un `super_admin`       |


```mermaid
sequenceDiagram
    participant Utilisateur
    participant Frontend
    participant API
    participant DB
    participant SuperAdmin

    Utilisateur->>Frontend: Remplit formulaire "Créer entreprise"
    Frontend->>API: POST /register-company (infos entreprise + user)
    API->>DB: Crée l'utilisateur (user)
    API->>DB: Crée l'entreprise (company avec is_approved=false)
    API-->>Frontend: Message: "Demande envoyée, en attente de validation"

    SuperAdmin->>Frontend: Se connecte (ROLE_SUPER_ADMIN)
    Frontend->>API: GET /admin/pending-companies
    API->>DB: Récupère les entreprises non approuvées
    API-->>Frontend: Liste des entreprises à valider

    SuperAdmin->>Frontend: Clique "Valider entreprise"
    Frontend->>API: PATCH /admin/companies/{id}/approve
    API->>DB: Met à jour company (is_approved=true, approved_by_id, approved_at)
    API-->>Frontend: Message: "Entreprise validée"

```

### Diagramme de class

```mermaid
classDiagram

%% Relations principales
User "1" --> "*" Equipment
User "1" --> "*" Intervention
User "1" --> "*" AppointmentRequest
Company "1" --> "*" User
Company "1" --> "*" Intervention
Company "1" --> "*" AppointmentRequest
TypeIntervention "1" --> "*" Intervention
TypeIntervention "1" --> "*" AppointmentRequest
Status "1" --> "*" Intervention
OperatingSystem "1" --> "*" Equipment
TypeEquipment "1" --> "*" Equipment
Brand "1" --> "*" Equipment
Intervention "1" --> "*" TaskIntervention
Task "1" --> "*" TaskIntervention
AppointmentRequest "1" --> "1" Intervention

%% Entité principale : User
class User {
  +int id
  +string email
  +json roles
  +string password
  +string first_name
  +string last_name
  +string phone
  +datetime created_at
  +datetime updated_at
  +string city
  +string zipcode
  +string address
}

class Company {
  +int id
  +string name
  +string about
  +string type
  +string address
  +string city
  +string zip_code
  +string website
  +datetime created_at
  +datetime updated_at
  +create()
  +edit()
  +delete()
  +update()
  +getList()
  +getDetails()
  +getUsers()
}

class Equipment {
  +int id
  +string name
  +datetime created_at
  +datetime updated_at
  +create()
  +edit()
  +delete()
  +update()
  +getEquipmentUser()
  +getOneEquipment()
}

class Intervention {
  +int id
  +string title
  +string description
  +datetime created_at
  +datetime updated_at
  +datetime start_date
  +datetime end_date
  +string text
  +createInterventionOnly()
  +edit()
  +delete()
  +getInterventionsList()
  +getInterventionsByCustomer()
  +changeStatusIntervention()
}

class AppointmentRequest {
  +int id
  +datetime date
  +string status
  +string description
  +string title
  +datetime created_at
  +datetime updated_at
  +getAvailableSlots()
  +addAppointment()
  +getAppointmentList()
  +insertAppointmentToIntervention()
  +editAppointment()
  +deleteAppointment()
  +getOneAppointment()
}

class TypeIntervention {
  +int id
  +string name
  +datetime created_at
  +datetime updated_at
}

class Status {
  +int id
  +string name
}

class Brand {
  +int id
  +string name
  +string logo
}

class TypeEquipment {
  +int id
  +string name
}

class OperatingSystem {
  +int id
  +string name
}

class Task {
  +int id
  +string name
  +float price
  +string description
}

class TaskIntervention {
  +int id
}

```

### Diagramme de composants / Architecture

Composant 1 : Application Mobile (React Native)

    Responsabilités : Interface utilisateur, gestion des états locaux, appels à l'API.

Composant 2 : API REST (Symfony)

    Responsabilités : Logique métier, authentification/autorisation, gestion des requêtes HTTP, interaction avec la base de données.

Composant 3 : Base de Données (PostgreSQL)

    Responsabilités : Stockage persistant des données (utilisateurs, entreprises, appareils, interventions).

Flux de données :

+---------------------+             +---------------------+             +---------------------+
|                     |             |                     |             |                     |
|  Application Mobile | <---------> |      API REST       | <---------> |    Base de Données  |
|    (React Native)   |   (HTTP)    |      (Symfony)      |   (SQL/ORM) |     (PostgreSQL)    |
|                     |             |                     |             |                     |
+---------------------+             +---------------------+             +---------------------+

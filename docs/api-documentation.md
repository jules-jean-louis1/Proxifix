# Documentation API - mobile-cda

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentification
Utilise JWT tokens via Lexik JWT Authentication Bundle.

## 👤 Users

### Lister les utilisateurs
```http
GET /api/user?search={query}&page={page}&size={size}&role={role}
```

**Paramètres :**
- `search` : Recherche dans nom, prénom, email
- `page` : Numéro de page (défaut: 1)
- `size` : Taille de page (défaut: 25)
- `role` : Filtrer par rôle (défaut: ROLE_CUSTOMER)

### Créer un utilisateur
```http
POST /api/user
```

**Body :**
```json
{
  "email": "user@example.com",
  "first_name": "John",
  "last_name": "Doe",
  "password": "password123",
  "role": ["ROLE_CUSTOMER"],
  "company_id": 1,
  "phone": "0123456789",
  "address": "123 Main St",
  "city": "Paris",
  "zip_code": "75001"
}
```

### Modifier un utilisateur
```http
PATCH /api/user/{id}
```

### Supprimer un utilisateur
```http
DELETE /api/user/{id}
```

## 🔧 Interventions

### Lister les interventions
```http
GET /api/intervention?user_id={id}&company_id={id}&status={status}
```

### Créer une intervention
```http
POST /api/intervention
```

**Body :**
```json
{
  "title": "Réparation machine",
  "description": "Description détaillée",
  "type_intervention_id": 1,
  "user_id": 123,
  "equipment_id": 456,
  "company_id": 1,
  "start_date": "2025-07-15T10:00:00Z",
  "end_date": "2025-07-15T12:00:00Z",
  "task": [
    {"id": 1},
    {"id": 2}
  ]
}
```

### Assigner un technicien
```http
PATCH /api/intervention/{id}/assign
```

**Body :**
```json
{
  "technician_id": 789
}
```

### Modifier une intervention
```http
PATCH /api/intervention/{id}
```

### Détails d'une intervention
```http
GET /api/intervention/{id}
```

### Supprimer une intervention
```http
DELETE /api/intervention/{id}
```

## 📋 Types d'Intervention

### Lister les types
```http
GET /api/type-intervention?name={search}&page={page}&size={size}
```

### Créer un type
```http
POST /api/type-intervention
```

**Body :**
```json
{
  "name": "Maintenance préventive",
  "description": "Maintenance régulière des équipements",
  "company_id": 1
}
```

### Modifier un type
```http
PUT /api/type-intervention/{id}
```

### Supprimer un type
```http
DELETE /api/type-intervention/{id}
```

## 📅 Créneaux Libres

### Obtenir les créneaux disponibles
```http
GET /api/appointment/free-slots?date={date}&company_id={id}&interval={minutes}&start_time={time}&end_time={time}&role={role}
```

## 🏢 Entreprises

### Créer une entreprise
```http
POST /api/company
```

**Body :**
```json
{
  "name": "Mon Entreprise",
  "email": "contact@entreprise.com",
  "phone": "0123456789",
  "address": "123 Business St",
  "city": "Paris",
  "zip_code": "75001"
}
```

## Codes de Statut HTTP

- `200 OK` : Succès
- `201 Created` : Ressource créée
- `400 Bad Request` : Erreur dans la requête
- `401 Unauthorized` : Non authentifié
- `403 Forbidden` : Accès refusé
- `404 Not Found` : Ressource non trouvée
- `500 Internal Server Error` : Erreur serveur

## Formats de Réponse

### Succès
```json
{
  "success": "Message de succès",
  "data": { ... },
  "users": [...],
  "interventions": [...]
}
```

### Erreur
```json
{
  "error": "Message d'erreur descriptif"
}
```

# Logique Métier - mobile-cda

## Vue d'ensemble

Cette application de gestion d'interventions permet aux entreprises de gérer leurs interventions techniques, équipements et clients.

## Entités Principales

### 🏢 Company (Entreprise)
- Représente une entreprise cliente
- Peut avoir plusieurs utilisateurs et équipements
- Point central de gestion des interventions

### 👤 User (Utilisateur)
- **ROLE_CUSTOMER** : Client final
- **ROLE_TECHNICIAN** : Technicien intervenant
- **ROLE_ADMIN** : Administrateur de l'entreprise
- **ROLE_SUPER_ADMIN** : Super administrateur système

### 🔧 Equipment (Équipement)
- Appartient à un utilisateur (customer)
- Peut faire l'objet de plusieurs interventions
- Contient les informations techniques

### 📋 Intervention
- Cœur métier de l'application
- Workflow : `pending` → `assigned` → `in_progress` → `completed`
- Lie un customer, un technician, et un équipement

### 📅 AppointmentRequest
- Demande de rendez-vous initiale
- Peut être convertie en intervention
- Contient les créneaux disponibles

## Workflows Métier

### 1. Création d'Intervention
```
Customer fait une demande → AppointmentRequest créée → Admin valide → Intervention créée (status: pending)
```

### 2. Assignation de Technicien
```
Intervention pending → Admin assigne technicien → Status: assigned
```

### 3. Exécution de l'Intervention
```
Status: assigned → Technicien démarre → Status: in_progress → Fin → Status: completed
```

## Règles Métier

### Sécurité et Permissions
- Les customers ne voient que leurs propres données
- Les admins gèrent leur entreprise uniquement
- Les super_admins ont accès global

### Contraintes d'Assignation
- Seuls les ROLE_TECHNICIAN et ROLE_ADMIN peuvent être assignés
- Une intervention ne peut être assignée que si status = pending ou assigned
- Un équipement doit appartenir au customer de l'intervention

### Gestion des Statuts
- `pending` : Intervention créée, en attente d'assignation
- `assigned` : Technicien assigné, intervention planifiée
- `in_progress` : Intervention en cours d'exécution
- `completed` : Intervention terminée
- `cancelled` : Intervention annulée

## Types d'Intervention

Les TypeIntervention permettent de catégoriser les interventions :
- Maintenance préventive
- Réparation d'urgence
- Installation
- Diagnostic
- etc.

Chaque type peut avoir des tâches spécifiques associées.

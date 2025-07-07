# Cahier de recette (plan de tests fonctionnels)

## API Company

### 1.1 Création d’une company (POST /api/company)
- [ ] Création avec tous les champs obligatoires et optionnels (name, type, address, city, zip_code, etc.)
- [ ] Ajout de spécialisations par id, par slug, et sous forme d’objet `{id: ...}` ou `{slug: ...}`
- [ ] Ajout de tâches (création de nouvelles tâches)
- [ ] Ajout de type_equipments et type_interventions (création systématique)
- [ ] Ajout d’utilisateurs existants (hors ROLE_CUSTOMER)
- [ ] Réponse 201 avec le JSON attendu (groupes company:read)

### 1.2 Gestion des erreurs
- [ ] Erreur si le type n’existe pas
- [ ] Erreur si le nom est manquant
- [ ] Erreur si un utilisateur n’existe pas ou est ROLE_CUSTOMER
- [ ] Erreur si une spécialisation n’existe pas

### 1.3 Lecture (GET)
- [ ] GET /api/company retourne la liste paginée
- [ ] GET /api/company/{id} retourne la fiche complète

### 1.4 Mise à jour (PUT)
- [ ] Modification des champs simples
- [ ] Modification des spécialisations, tâches, équipements, interventions

### 1.5 Suppression (DELETE)
- [ ] Suppression d’une company
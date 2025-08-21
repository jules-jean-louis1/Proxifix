# 🚀 Génération des Types TypeScript

Ce projet propose plusieurs façons de générer les types TypeScript depuis votre API Platform.

## 📁 Structure

```
proxifix/
├── docker/
│   ├── generate-types.sh          # Script bash global
│   └── .env.tpl                   # Configuration des ports
├── mobile/
│   ├── scripts/
│   │   └── generate-types.js      # Script Node.js local
│   ├── package.json               # Dépendances et scripts mobile
│   └── .env                       # Configuration API URL
└── package.json                   # Scripts globaux du projet
```

## 🛠️ Méthodes de génération

### 1. Depuis le dossier mobile (recommandé pour le développement mobile)

```bash
cd mobile
npm run generate:types
```

### 2. Depuis la racine du projet

```bash
npm run generate:types
# ou directement
./docker/generate-types.sh
```

### 3. Depuis le dossier mobile avec script spécifique

```bash
cd mobile
npm run mobile:types
```

## ⚙️ Configuration

### Variables d'environnement

**mobile/.env:**
```env
EXPO_PUBLIC_API_URL=http://localhost:81/api
```

**docker/.env.tpl:**
```env
NGINX_PORT=81
```

## 🔧 Fonctionnalités

- ✅ **Chemins relatifs** : Plus de chemins absolus
- ✅ **Variables d'environnement** : Port configurable depuis les fichiers .env
- ✅ **Détection d'erreurs** : Messages clairs en cas de problème
- ✅ **Test de connectivité** : Vérification automatique de l'API
- ✅ **Installation automatique** : Installation d'openapi-typescript si nécessaire
- ✅ **Messages colorés** : Interface utilisateur améliorée

## 🐛 Dépannage

### L'API n'est pas accessible
```bash
# Vérifiez que votre environnement Docker est démarré
cd docker && docker-compose up -d

# Testez manuellement l'accès à l'API
curl http://localhost:81/api/docs.json
```

### Port incorrect
Vérifiez la configuration dans `docker/.env.tpl` ou `mobile/.env`

### openapi-typescript non installé
Le script l'installe automatiquement, mais vous pouvez le faire manuellement :
```bash
cd mobile && npm install --save-dev openapi-typescript
```

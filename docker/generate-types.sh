#!/bin/bash

# Script pour générer les types TypeScript depuis l'API
# Ce script peut être exécuté depuis n'importe où dans le projet

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Génération des types TypeScript pour l'API...${NC}"

# Déterminer le répertoire racine du projet
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Charger les variables d'environnement depuis le fichier .env.tpl
if [ -f "$SCRIPT_DIR/.env" ]; then
    source "$SCRIPT_DIR/.env"
elif [ -f "$SCRIPT_DIR/.env.tpl" ]; then
    source "$SCRIPT_DIR/.env.tpl"
else
    echo -e "${RED}❌ Fichier .env ou .env.tpl non trouvé dans le dossier docker${NC}"
    exit 1
fi

# Utiliser le port NGINX_PORT du fichier d'environnement
NGINX_PORT=${NGINX_PORT:-81}
API_URL="http://localhost:${NGINX_PORT}/api"
DOCS_URL="http://localhost:${NGINX_PORT}/api/docs.json"

# Chemins relatifs
MOBILE_DIR="$PROJECT_ROOT/mobile"
OUTPUT_FILE="$MOBILE_DIR/app/types/types.ts"

echo -e "${BLUE}📡 URL de documentation: ${DOCS_URL}${NC}"
echo -e "${BLUE}📝 Fichier de sortie: ${OUTPUT_FILE}${NC}"

# Vérifier que le dossier mobile existe
if [ ! -d "$MOBILE_DIR" ]; then
    echo -e "${RED}❌ Dossier mobile non trouvé: $MOBILE_DIR${NC}"
    exit 1
fi

# Aller dans le dossier mobile pour exécuter npm
cd "$MOBILE_DIR"

# Vérifier que openapi-typescript est installé
if ! npm list openapi-typescript --depth=0 >/dev/null 2>&1; then
    echo -e "${YELLOW}⚠️ openapi-typescript n'est pas installé, installation...${NC}"
    npm install --save-dev openapi-typescript
fi

# Tester si l'API est accessible
echo -e "${BLUE}🔍 Test de connectivité à l'API...${NC}"
if curl -s --max-time 5 "$DOCS_URL" >/dev/null; then
    echo -e "${GREEN}✅ API accessible${NC}"
else
    echo -e "${RED}❌ Impossible d'accéder à l'API${NC}"
    echo -e "${YELLOW}💡 Solutions possibles:${NC}"
    echo -e "   1. Démarrez votre environnement Docker: cd docker && docker-compose up -d"
    echo -e "   2. Vérifiez que le port $NGINX_PORT est correct"
    echo -e "   3. Testez manuellement: curl $DOCS_URL"
    exit 1
fi

# Générer les types
echo -e "${BLUE}🔨 Génération des types...${NC}"
npx openapi-typescript "$DOCS_URL" -o "$OUTPUT_FILE"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Types TypeScript générés avec succès!${NC}"
    echo -e "${GREEN}📁 Fichier créé: $OUTPUT_FILE${NC}"
else
    echo -e "${RED}❌ Erreur lors de la génération des types${NC}"
    exit 1
fi

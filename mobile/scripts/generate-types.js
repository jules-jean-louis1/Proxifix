#!/usr/bin/env node

const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

// Fonction pour lire le fichier .env
function loadEnv() {
  const envPath = path.join(__dirname, '../.env');
  if (!fs.existsSync(envPath)) {
    console.error('❌ Fichier .env non trouvé dans le dossier mobile');
    console.log('💡 Assurez-vous que le fichier .env existe avec EXPO_PUBLIC_API_URL');
    process.exit(1);
  }

  const envContent = fs.readFileSync(envPath, 'utf8');
  const env = {};
  
  envContent.split('\n').forEach(line => {
    const [key, value] = line.split('=');
    if (key && value) {
      env[key.trim()] = value.trim();
    }
  });

  return env;
}

// Charger les variables d'environnement
const env = loadEnv();
const apiUrl = env.EXPO_PUBLIC_API_URL;

if (!apiUrl) {
  console.error('❌ Variable EXPO_PUBLIC_API_URL non trouvée dans .env');
  console.log('💡 Ajoutez EXPO_PUBLIC_API_URL=http://localhost:81/api dans votre fichier .env');
  process.exit(1);
}

// Construire l'URL de documentation OpenAPI
const docsUrl = `${apiUrl.replace('/api', '')}/api/docs.json`;
const outputPath = path.join(__dirname, '../app/types/types.ts');

console.log('🚀 Génération des types TypeScript...');
console.log(`📡 URL de documentation: ${docsUrl}`);
console.log(`📝 Fichier de sortie: ${outputPath}`);

// Commande openapi-typescript
const command = `npx openapi-typescript "${docsUrl}" -o "${outputPath}"`;

console.log(`🔨 Exécution: ${command}`);

exec(command, (error, stdout, stderr) => {
  if (error) {
    console.error('❌ Erreur lors de la génération des types:');
    console.error(error.message);
    
    if (error.message.includes('ECONNREFUSED')) {
      console.log('\n💡 Solutions possibles:');
      console.log('   1. Vérifiez que votre serveur API est démarré');
      console.log('   2. Vérifiez que l\'URL dans .env est correcte');
      console.log('   3. Testez manuellement: curl ' + docsUrl);
    }
    
    process.exit(1);
  }

  if (stderr && !stderr.includes('deprecated')) {
    console.warn('⚠️ Avertissements:', stderr);
  }

  if (stdout) {
    console.log(stdout);
  }

  console.log('✅ Types TypeScript générés avec succès!');
  console.log(`📁 Fichier créé: ${outputPath}`);
});

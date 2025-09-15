#!/usr/bin/env node
const fs = require('fs');
const path = require('path');

// Récupération de la version passée en argument
const version = process.argv[2];
if (!version) {
  console.error('Usage: node set-composer-version.cjs <version>');
  process.exit(1);
}

// Chemin vers composer.json
const file = path.resolve(__dirname, '..', 'composer.json');

// Lecture, modification et écriture du fichier
try {
  const json = JSON.parse(fs.readFileSync(file, 'utf8'));
  json.version = version;
  fs.writeFileSync(file, JSON.stringify(json, null, 2) + '\n', 'utf8');
  console.log(`composer.json updated to ${version}`);
} catch (err) {
  console.error('Error updating composer.json:', err);
  process.exit(1);
}

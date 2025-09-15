#!/usr/bin/env node
const fs = require('fs');
const path = require('path');

const version = process.argv[2];
if (!version) {
  console.error('Usage: node set-composer-version.js <version>');
  process.exit(1);
}

const file = path.resolve(__dirname, '..', 'composer.json');

const json = JSON.parse(fs.readFileSync(file, 'utf8'));
json.version = version;
fs.writeFileSync(file, JSON.stringify(json, null, 2) + '\n', 'utf8');

console.log(`composer.json updated to ${version}`);

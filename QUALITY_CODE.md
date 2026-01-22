# Tests de qualité de code PHP

Ce projet utilise trois outils pour garantir la qualité du code :

## 1. PHP_CodeSniffer (PSR2)

Vérifie que le code respecte les standards PSR2.

### Configuration
- Fichier : `phpcs.xml.dist`
- Standard : PSR2

### Commandes

```bash
# Vérifier le code
vendor/bin/phpcs --standard=PSR2 src/

# Corriger automatiquement les erreurs
vendor/bin/phpcbf --standard=PSR2 src/
```

### Résultat attendu
✅ Code conforme PSR2 (0 erreurs)

## 2. PHPStan (Niveau 2)

Analyse statique du code pour détecter les erreurs potentielles.

### Configuration
- Fichier : `phpstan.dist.neon`
- Niveau : 2

### Commande

```bash
vendor/bin/phpstan analyze src/ --level=2
```

### Résultat attendu
✅ Aucune erreur détectée

## 3. PHPMD (PHP Mess Detector)

Détecte les problèmes de qualité de code (complexité, design, etc.).

### Configuration
- Fichier : `phpmd.xml`
- Règles : cleancode, codesize, controversial, design
- Règles exclues : naming (pour éviter les erreurs sur variables courtes comme `$id`)

### Commande

```bash
vendor/bin/phpmd src/ text cleancode,codesize,controversial,design
```

Ou avec le fichier de configuration :

```bash
vendor/bin/phpmd src/ text phpmd.xml
```

## Scripts Composer (optionnel)

Vous pouvez ajouter ces scripts dans `composer.json` :

```json
{
  "scripts": {
    "quality": [
      "@quality:phpcs",
      "@quality:phpstan",
      "@quality:phpmd"
    ],
    "quality:phpcs": "vendor/bin/phpcs --standard=PSR2 src/",
    "quality:phpcbf": "vendor/bin/phpcbf --standard=PSR2 src/",
    "quality:phpstan": "vendor/bin/phpstan analyze src/ --level=2",
    "quality:phpmd": "vendor/bin/phpmd src/ text phpmd.xml"
  }
}
```

Puis exécuter :

```bash
composer quality
```

## Résultats actuels

✅ **PHP_CodeSniffer (PSR2)** : Code conforme  
✅ **PHPStan (Niveau 2)** : Aucune erreur  
⚠️ **PHPMD** : Quelques warnings (non bloquants)

Les warnings PHPMD concernent principalement :
- Complexité cyclomatique dans certaines méthodes
- Accès statiques (normal pour les fixtures)
- Imports manquants (non critiques)

Ces warnings n'empêchent pas le code de fonctionner et peuvent être ignorés ou corrigés progressivement.

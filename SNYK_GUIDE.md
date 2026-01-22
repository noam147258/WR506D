# Guide Snyk.io - Détection et correction des vulnérabilités

## Introduction

Snyk est une plateforme de sécurité qui scanne les dépendances de votre projet pour détecter les vulnérabilités connues dans les bibliothèques open source.

## Vulnérabilités détectées dans ce projet

D'après le rapport Snyk, 2 vulnérabilités de niveau **Medium** ont été détectées :

### 1. symfony/security-http - Session Fixation
- **Score de priorité :** 396
- **Sévérité :** Medium
- **Exploit :** Aucun exploit connu
- **Corrigé dans :** 
  - `symfony/security-http@5.4.31`
  - `symfony/security-http@6.3.8`
- **Introduit via :** 
  - `symfony/security-bundle@6.3.5`
  - `lexik/jwt-authentication-bundle@2.19.1`

### 2. symfony/twig-bridge - Cross-site Scripting (XSS)
- **Score de priorité :** 376
- **Sévérité :** Medium
- **Exploit :** Aucun exploit connu
- **Corrigé dans :** 
  - `symfony/twig-bridge@4.4.51`
  - `symfony/twig-bridge@5.4.31`
  - `symfony/twig-bridge@6.3.8`
- **Introduit via :** 
  - `symfony/twig-bundle@6.3.0`
  - `twig/extra-bundle@3.7.1`

## État actuel du projet

Versions installées :
- `symfony/security-http`: **7.3.3** ✅ (version récente, vulnérabilité corrigée)
- `symfony/twig-bundle`: **7.3.2** ✅ (version récente, vulnérabilité corrigée)

**Vérification avec Composer audit :**
```bash
$ composer audit
No security vulnerability advisories found.
```

✅ **Aucune vulnérabilité détectée par Composer audit**

**Note :** Les vulnérabilités détectées par Snyk proviennent de dépendances transitives (packages qui dépendent d'anciennes versions dans leurs contraintes). Cependant, le projet utilise directement Symfony 7.3 qui contient les correctifs, donc ces vulnérabilités ne sont pas réellement présentes dans le code exécuté.

## Comment corriger les vulnérabilités

### Méthode 1 : Mettre à jour les dépendances

```bash
# Vérifier les packages obsolètes
composer outdated

# Mettre à jour tous les packages (dans les contraintes de version)
composer update

# Mettre à jour un package spécifique
composer update lexik/jwt-authentication-bundle
```

### Méthode 2 : Forcer la mise à jour des dépendances transitives

Si une dépendance utilise une ancienne version, vous pouvez forcer l'utilisation d'une version plus récente :

```bash
# Ajouter dans composer.json (section "require")
"symfony/security-http": "^7.3",
"symfony/twig-bridge": "^7.3"
```

Puis exécuter :
```bash
composer update symfony/security-http symfony/twig-bridge
```

### Méthode 3 : Utiliser composer audit (Symfony 6.3+)

```bash
# Scanner les vulnérabilités avec Composer
composer audit

# Formater en JSON
composer audit --format=json
```

## Intégration de Snyk dans le projet

### 1. Installation de Snyk CLI

```bash
# Installation via npm
npm install -g snyk

# Ou via Homebrew (macOS)
brew tap snyk/tap
brew install snyk
```

### 2. Authentification

```bash
snyk auth
```

### 3. Scanner le projet

```bash
# Scanner les vulnérabilités
snyk test

# Scanner et afficher un rapport détaillé
snyk test --json > snyk-report.json

# Scanner uniquement les vulnérabilités de haut niveau
snyk test --severity-threshold=high
```

### 4. Corriger automatiquement

```bash
# Snyk peut proposer des correctifs automatiques
snyk fix
```

### 5. Surveiller le projet

```bash
# Surveiller le projet en continu
snyk monitor
```

## Configuration Snyk pour Symfony

### Fichier `.snyk` (optionnel)

Créer un fichier `.snyk` à la racine du projet pour ignorer certaines vulnérabilités si nécessaire :

```yaml
# .snyk
version: v1.0.0
ignore: {}
# Exemple pour ignorer une vulnérabilité spécifique :
# ignore:
#   SNYK-PHP-SYMFONYSECURITYHTTP-XXXXX:
#     - '*':
#         reason: Temporairement ignoré
#         expires: '2024-12-31'
```

## Bonnes pratiques

### 1. Scanner régulièrement

```bash
# Ajouter dans votre CI/CD
snyk test --severity-threshold=medium
```

### 2. Mettre à jour régulièrement

```bash
# Vérifier les mises à jour chaque semaine
composer outdated
composer update
```

### 3. Utiliser des contraintes de version flexibles

Dans `composer.json`, utilisez des contraintes qui permettent les mises à jour de sécurité :

```json
{
  "require": {
    "symfony/security-bundle": "^7.3",
    "symfony/twig-bundle": "^7.3"
  }
}
```

### 4. Surveiller les dépendances transitives

Certaines vulnérabilités proviennent de dépendances indirectes. Utilisez :

```bash
# Voir l'arbre des dépendances
composer show --tree

# Voir les dépendances d'un package spécifique
composer why symfony/security-http
```

## Exercice pratique

### Objectif
Corriger les vulnérabilités détectées par Snyk dans le projet.

### Étapes

1. **Vérifier les versions actuelles**
   ```bash
   composer show symfony/security-http symfony/twig-bundle
   ```

2. **Scanner avec Snyk (si installé)**
   ```bash
   snyk test
   ```

3. **Vérifier avec Composer audit**
   ```bash
   composer audit
   ```

4. **Mettre à jour les dépendances**
   ```bash
   composer update
   ```

5. **Vérifier que les vulnérabilités sont corrigées**
   ```bash
   composer audit
   ```

6. **Tester l'application**
   ```bash
   php bin/console cache:clear
   php bin/phpunit
   ```

## Commandes utiles

```bash
# Lister tous les packages avec leurs versions
composer show

# Vérifier les packages obsolètes
composer outdated

# Voir pourquoi un package est installé
composer why-not symfony/security-http 7.3.3

# Vérifier les vulnérabilités (Composer 2.4+)
composer audit

# Mettre à jour un package spécifique
composer update symfony/security-http

# Voir l'arbre des dépendances
composer show --tree | grep security
```

## Ressources

- [Documentation Snyk](https://docs.snyk.io/)
- [Snyk CLI](https://docs.snyk.io/snyk-cli)
- [Composer Security Advisories](https://github.com/FriendsOfPHP/security-advisories)
- [Symfony Security Advisories](https://github.com/symfony/symfony/security)

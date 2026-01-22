# GitHub Actions - CI/CD Pipeline

## Workflow configuré

**Fichier :** `.github/workflows/php-ci.yml`

## Étapes du pipeline

Le workflow `symfony-tests` s'exécute automatiquement sur :
- Push vers les branches `develop` et `main`
- Pull requests vers les branches `develop` et `main`

### Étapes d'exécution

1. **Setup PHP 8.3**
   - Installation de PHP avec les extensions nécessaires
   - Extensions : mbstring, xml, ctype, iconv, intl, pdo, pdo_mysql, zip

2. **Checkout du code**
   - Récupération du code source depuis le dépôt

3. **Configuration de l'environnement**
   - Copie de `.env.test` vers `.env.test.local` si nécessaire

4. **Cache Composer**
   - Mise en cache des packages Composer pour accélérer les builds suivants

5. **Installation des dépendances**
   - `composer install` avec optimisations

6. **Service MySQL**
   - Base de données MySQL 8.0 pour les tests
   - Base : `symfony_test`
   - Utilisateur : `root` / Mot de passe : `root`

7. **Création de la base de données**
   - Création du schéma de base de données pour les tests

8. **Exécution des tests PHPUnit**
   - Tous les tests unitaires et fonctionnels
   - Test spécifique de la documentation API

9. **Vérification de la qualité du code**
   - **PHP_CodeSniffer** : Vérification PSR2
   - **PHPStan** : Analyse statique niveau 2
   - **PHPMD** : Détection de problèmes de code

## Résultats attendus

✅ **PHPUnit** : Tous les tests doivent passer  
✅ **PHP_CodeSniffer** : Code conforme PSR2 (0 erreurs)  
✅ **PHPStan** : Aucune erreur détectée  
⚠️ **PHPMD** : Quelques warnings acceptables (complexité, imports statiques)

## Commandes locales équivalentes

Pour tester localement avant de pousser :

```bash
# Tests PHPUnit
vendor/bin/phpunit

# Qualité de code
vendor/bin/phpcs --standard=PSR2 src/
vendor/bin/phpstan analyze src/ --level=2
vendor/bin/phpmd src/ text cleancode,codesize,controversial,design
```

## Voir les résultats

Les résultats du workflow sont visibles dans l'onglet **Actions** de votre dépôt GitHub.

## Améliorations possibles

- Ajouter des tests de couverture de code
- Intégrer Snyk pour la détection de vulnérabilités
- Ajouter des tests d'intégration
- Notification en cas d'échec (Slack, email, etc.)

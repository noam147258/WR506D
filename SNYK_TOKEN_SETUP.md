# Configuration Snyk avec Token (Solution au problème de port 8080)

## Problème

L'authentification OAuth de Snyk nécessite que le port 8080 soit libre pour recevoir le callback. Si Apache ou un autre service utilise déjà ce port, l'authentification échoue.

## Solution : Utiliser un token d'authentification

### Étape 1 : Obtenir un token Snyk

**⚠️ IMPORTANT : Vous devez aller dans les paramètres de VOTRE COMPTE, pas ceux de l'organisation !**

1. **Connectez-vous à Snyk**
   - Allez sur https://app.snyk.io
   - Connectez-vous avec votre compte

2. **Accéder aux paramètres de votre compte personnel**
   - Cliquez sur votre **avatar/profil** (icône ronde en haut à droite, à côté de l'icône ⚙️)
   - OU cliquez sur l'icône **⚙️ Settings** puis sélectionnez **Account** (pas "Organization settings")
   - Assurez-vous d'être dans **"Account"** et non dans **"Organization settings"**

3. **Créer un token**
   - Dans le menu de gauche, cherchez la section **"Auth Token"** ou **"API Token"**
   - Cliquez sur **Generate Token** ou **Create Token** ou **Add Token**
   - Donnez un nom au token (ex: "CLI Development")
   - Cliquez sur **Generate** ou **Create**

4. **Copier le token**
   - ⚠️ **IMPORTANT** : Copiez le token immédiatement, vous ne pourrez plus le voir après !
   - Le token ressemble à : `abc123def456ghi789jkl012mno345pqr678stu901vwx234yz`

**Note :** Si vous ne voyez toujours pas l'option "Auth Token", essayez :
- De cliquer directement sur votre avatar en haut à droite
- De chercher "API" ou "Token" dans les paramètres
- D'utiliser cette URL directe : https://app.snyk.io/account

### Étape 2 : Configurer le token dans le CLI

Dans votre terminal, exécutez :

```bash
snyk config set api=<votre-token>
```

**Remplacez `<votre-token>` par le token que vous avez copié.**

**Exemple :**
```bash
snyk config set api=abc123def456ghi789jkl012mno345pqr678stu901vwx234yz
```

### Étape 3 : Vérifier l'authentification

```bash
snyk auth status
```

Vous devriez voir un message confirmant que vous êtes authentifié.

### Étape 4 : Tester Snyk

```bash
# Scanner votre projet
snyk test

# Scanner et afficher un rapport détaillé
snyk test --json > snyk-report.json
```

## Commandes utiles

```bash
# Vérifier le statut d'authentification
snyk auth status

# Voir la configuration actuelle
snyk config get api

# Scanner le projet
snyk test

# Scanner avec un seuil de sévérité
snyk test --severity-threshold=high

# Surveiller le projet
snyk monitor
```

## Sécurité

⚠️ **Ne commitez jamais votre token dans Git !**

Si vous avez accidentellement committé un token :
1. Régénérez un nouveau token dans Snyk
2. Supprimez l'ancien token
3. Configurez le nouveau token avec `snyk config set api=<nouveau-token>`

## Dépannage

### Erreur "Authentication error"

Vérifiez que le token est correct :
```bash
snyk config get api
```

Si le token est incorrect, configurez-le à nouveau :
```bash
snyk config set api=<votre-token>
```

### Le token a expiré

Créez un nouveau token dans Snyk et reconfigurez-le.

# Guide d'authentification Snyk

## Problème rencontré

Lors de l'exécution de `snyk auth`, une page phpMyAdmin s'ouvre au lieu de la page d'authentification Snyk. C'est probablement dû à une redirection locale de votre environnement.

## Solution : Authentification manuelle

### Étape 1 : Obtenir l'URL d'authentification

Exécutez la commande :
```bash
snyk auth
```

Vous verrez une URL similaire à :
```
https://app.snyk.io/oauth2/authorize?access_type=offline&client_id=...
```

### Étape 2 : Copier l'URL

Copiez l'URL complète qui commence par `https://app.snyk.io/oauth2/authorize...`

### Étape 3 : Ouvrir l'URL dans votre navigateur

1. Ouvrez votre navigateur web
2. Collez l'URL dans la barre d'adresse
3. Appuyez sur Entrée

### Étape 4 : Se connecter à Snyk

1. Si vous avez déjà un compte Snyk, connectez-vous
2. Si vous n'avez pas de compte, créez-en un gratuitement sur https://snyk.io
3. Autorisez l'accès à votre compte

### Étape 5 : Vérifier l'authentification

Une fois authentifié, retournez dans votre terminal et vérifiez :

```bash
snyk auth status
```

Vous devriez voir un message confirmant que vous êtes authentifié.

## Alternative : Authentification via token (RECOMMANDÉ si port 8080 occupé)

Si l'authentification via navigateur ne fonctionne pas (conflit de port avec Apache), utilisez un token :

### 1. Obtenir un token depuis Snyk

1. Connectez-vous sur https://app.snyk.io
2. Allez dans **Settings** (icône engrenage en haut à droite)
3. Cliquez sur **Account** dans le menu de gauche
4. Allez dans la section **Auth Token**
5. Cliquez sur **Generate Token** ou **Create Token**
6. Donnez un nom au token (ex: "CLI Local")
7. Copiez le token (vous ne pourrez plus le voir après !)

### 2. Configurer le token dans le CLI

```bash
snyk config set api=<votre-token>
```

Remplacez `<votre-token>` par le token que vous avez copié.

**Exemple :**
```bash
snyk config set api=abc123def456ghi789...
```

### 3. Vérifier

```bash
snyk auth status
```

Vous devriez voir un message confirmant que vous êtes authentifié.

### 4. Tester

```bash
snyk test
```

## Commandes utiles

```bash
# Vérifier le statut d'authentification
snyk auth status

# Se déconnecter
snyk auth logout

# Tester la connexion
snyk test
```

## Dépannage

### Le navigateur ne s'ouvre pas automatiquement

C'est normal dans certains environnements. Utilisez l'URL fournie manuellement.

### Timeout lors de l'authentification

1. Vérifiez votre connexion internet
2. Assurez-vous que le port 18081 n'est pas bloqué par un firewall
3. Utilisez l'authentification par token à la place

### Erreur "authentication failed"

Réessayez avec :
```bash
snyk auth
```

Ou utilisez l'authentification par token.

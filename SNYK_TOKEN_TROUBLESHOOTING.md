# Guide : Trouver le token Snyk - Dépannage

## Problème : Je ne trouve pas le bouton pour générer un token

### Solution 1 : Vérifier que vous êtes dans les bons paramètres

**❌ Ne pas aller dans :** Organization Settings > General  
**✅ Aller dans :** Account Settings (paramètres de votre compte personnel)

### Étapes détaillées :

1. **Cliquez sur votre avatar** (icône ronde en haut à droite)
   - C'est généralement votre photo de profil ou vos initiales
   - PAS l'icône ⚙️ Settings de l'organisation

2. **Ou utilisez l'URL directe :**
   ```
   https://app.snyk.io/account
   ```

3. **Dans le menu de gauche, cherchez :**
   - "Auth Token"
   - "API Token"  
   - "Personal API Token"
   - "Account Token"

### Solution 2 : Utiliser Service Accounts (alternative)

Si vous ne trouvez toujours pas l'option dans Account, vous pouvez créer un Service Account :

1. Allez dans **Organization Settings** > **General**
2. Dans la section **"Organization API key"**, cliquez sur **"Manage service accounts"**
3. Cliquez sur **"Create service account"**
4. Donnez un nom (ex: "CLI Token")
5. Copiez le token généré

**Note :** Les Service Accounts sont liés à l'organisation, mais fonctionnent aussi pour le CLI.

### Solution 3 : Vérifier votre plan Snyk

Certains plans gratuits peuvent avoir des limitations. Si vous ne voyez pas l'option :

1. Vérifiez que vous êtes bien connecté
2. Essayez de créer un nouveau compte Snyk si nécessaire
3. Contactez le support Snyk si le problème persiste

### Solution 4 : Utiliser l'authentification OAuth avec un autre port

Si vous préférez utiliser OAuth au lieu d'un token, vous pouvez configurer Snyk pour utiliser un autre port :

```bash
# Configurer Snyk pour utiliser le port 18081 au lieu de 8080
export SNYK_OAUTH_PORT=18081
snyk auth
```

Puis ouvrez l'URL fournie dans votre navigateur.

## Vérification

Une fois que vous avez le token, configurez-le :

```bash
snyk config set api=<votre-token>
snyk auth status
```

## Aide supplémentaire

- Documentation Snyk : https://docs.snyk.io/snyk-cli/authenticate-the-cli-with-snyk
- Support Snyk : https://support.snyk.io/

# Guide d'utilisation du JWT

## 1. Obtenir un token JWT

Envoyez une requête POST à `/auth` avec les identifiants :

```bash
curl -X POST http://localhost:8319/auth \
  -H "Content-Type: application/json" \
  -d '{
    "email": "votre_email@domaine.com",
    "password": "votre_mot_de_passe"
  }'
```

**Réponse réussie (200) :**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

## 2. Utiliser le token pour les requêtes API

Toutes les routes sous `/api/` sont protégées et nécessitent un token JWT valide.

### Exemple avec cURL

```bash
# Récupérer le token (stockez-le dans une variable)
TOKEN=$(curl -s -X POST http://localhost:8319/auth \
  -H "Content-Type: application/json" \
  -d '{"email":"votre_email@domaine.com","password":"votre_mot_de_passe"}' \
  | jq -r '.token')

# Utiliser le token pour accéder aux routes API
curl -X GET http://localhost:8319/api/movies \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Exemple avec JavaScript (fetch)

```javascript
// 1. Authentification
const authResponse = await fetch('http://localhost:8319/auth', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'votre_email@domaine.com',
    password: 'votre_mot_de_passe'
  })
});

const authData = await authResponse.json();
const token = authData.token;

// 2. Utiliser le token pour les requêtes API
const apiResponse = await fetch('http://localhost:8319/api/movies', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
});

const movies = await apiResponse.json();
console.log(movies);
```

### Exemple avec PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8319']);

// 1. Authentification
$authResponse = $client->post('/auth', [
    'json' => [
        'email' => 'votre_email@domaine.com',
        'password' => 'votre_mot_de_passe'
    ]
]);

$authData = json_decode($authResponse->getBody()->getContents(), true);
$token = $authData['token'];

// 2. Utiliser le token pour les requêtes API
$apiResponse = $client->get('/api/movies', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json'
    ]
]);

$movies = json_decode($apiResponse->getBody()->getContents(), true);
```

### Exemple avec Postman

1. **Créer une requête POST vers `/auth`**
   - Method: POST
   - URL: `http://localhost:8319/auth`
   - Headers: `Content-Type: application/json`
   - Body (raw JSON):
     ```json
     {
       "email": "votre_email@domaine.com",
       "password": "votre_mot_de_passe"
     }
     ```
   - Dans "Tests", ajoutez ce script pour sauvegarder le token :
     ```javascript
     var jsonData = pm.response.json();
     pm.environment.set("jwt_token", jsonData.token);
     ```

2. **Créer une requête GET vers `/api/movies`**
   - Method: GET
   - URL: `http://localhost:8319/api/movies`
   - Headers:
     - `Authorization: Bearer {{jwt_token}}`
     - `Content-Type: application/json`

## Routes API disponibles

Toutes les entités avec `#[ApiResource]` sont automatiquement exposées via API Platform :

- **Films** : `GET /api/movies`, `GET /api/movies/{id}`, `POST /api/movies`, etc.
- **Acteurs** : `GET /api/actors`, `GET /api/actors/{id}`, etc.
- **Réalisateurs** : `GET /api/directors`, `GET /api/directors/{id}`, etc.
- **Catégories** : `GET /api/categories`, `GET /api/categories/{id}`, etc.

## Gestion des erreurs

### Token manquant (401)
```json
{
  "code": 401,
  "message": "JWT Token not found"
}
```

### Token invalide ou expiré (401)
```json
{
  "code": 401,
  "message": "Invalid JWT Token"
}
```

### Accès refusé (403)
```json
{
  "code": 403,
  "message": "Access Denied."
}
```

## Durée de vie du token

Par défaut, le token JWT est valide pendant **3600 secondes (1 heure)**. Après expiration, vous devrez vous ré-authentifier pour obtenir un nouveau token.


#  Mini LinkedIn - API REST Laravel

Une API REST complète pour un mini-réseau social professionnel permettant aux candidats de postuler à des offres d'emploi et aux recruteurs de gérer leurs offres.

##  Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Démarrage](#-démarrage)
- [Structure du projet](#-structure-du-projet)
- [Routes API](#-routes-api)
- [Authentification](#-authentification)
- [Rôles et permissions](#-rôles-et-permissions)
- [Collection Postman](#-collection-postman)
- [Base de données](#-base-de-données)
- [Événements](#-événements)
- [Gestion des erreurs](#-gestion-des-erreurs)
- [Licence](#-licence)



##  Fonctionnalités

###  **Pour les candidats:**
-  Inscription et connexion avec JWT
-  Création et modification du profil
-  Gestion des compétences
-  Consultation des offres disponibles
-  Candidature à une offre
-  Suivi de ses candidatures

### **Pour les recruteurs:**
-  Création et gestion des offres d'emploi
-  Consultation des candidatures reçues
-  Modification du statut des candidatures (en attente, acceptée, refusée)
-  Suppression des offres

###  **Pour les administrateurs:**
-  Gestion des utilisateurs
-  Activation/Désactivation des offres
-  Suppression des comptes



##  Prérequis

- **PHP** >= 8.2
- **MySQL** >= 5.7 ou **MariaDB** >= 10.3
- **Composer** (gestionnaire de dépendances PHP)
- **Node.js** (optionnel, pour npm)
- **Postman** (pour tester l'API)



##  Installation

### **1. Cloner le dépôt**

```bash
git clone https://github.com/benraissara/Projet_Mini_LinkdIn.git
cd Projet_Mini_LinkdIn
```

### **2. Installer les dépendances PHP**

```bash
composer install
```

### **3. Copier le fichier d'environnement**

```bash
cp .env.example .env
```

### **4. Générer la clé d'application**

```bash
php artisan key:generate
```

---

##  Configuration

### **1. Configurer la base de données dans `.env`**

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projet_mini_linkdin
DB_USERNAME=root
DB_PASSWORD=
```

### **2. Configurer JWT (si nécessaire)**

```dotenv
JWT_SECRET=your_jwt_secret_key_here
```

### **3. (Optionnel) Configurer les emails**

```dotenv
MAIL_MAILER=log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="no-reply@minlinkedin.com"
```

---

##  Démarrage

### **1. Créer la base de données**

```bash
mysql -u root -p
mysql> CREATE DATABASE projet_mini_linkdin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
mysql> EXIT;
```

### **2. Exécuter les migrations**

```bash
php artisan migrate
```

### **3. (Optionnel) Remplir la base de données avec des données de test**

```bash
php artisan db:seed
```

### **4. Lancer le serveur de développement**

```bash
php artisan serve
```

L'API sera disponible à : `http://localhost:8000`

### **5. Pour exécuter le système de queue (événements)**

```bash
php artisan queue:listen
```

---

##  Structure du projet

```
Projet_Mini_LinkdIn/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ProfilController.php
│   │   │   ├── OffreController.php
│   │   │   ├── CandidatureController.php
│   │   │   └── AdminController.php
│   │   └── Middleware/
│   │       └── CheckRole.php
│   ├── Events/
│   │   ├── CandidatureDeposee.php
│   │   └── StatutCandidatureMis.php
│   ├── Listeners/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Profil.php
│   │   ├── Offre.php
│   │   ├── Candidature.php
│   │   └── Competence.php
│   └── Exceptions/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   ├── api.php
│   └── web.php
├── tests/
├── postman/
│   ├── Mini_LinkedIn_Collection.json
│   ├── environment.json
│   └── README.md
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

##  Routes API

### ** Routes publiques (sans authentification)**

#### **Authentification**
| Méthode | Route | Description |
|---------|-------|-------------|
| POST | `/api/register` | Inscription d'un nouvel utilisateur |
| POST | `/api/login` | Connexion d'un utilisateur |

#### **Offres d'emploi**
| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/api/offres` | Lister toutes les offres actives (paginées) |
| GET | `/api/offres/{id}` | Détail d'une offre |

---

### ** Routes protégées (authentification JWT requise)**

#### **Profil utilisateur (Candidat)**
| Méthode | Route | Rôle | Description |
|---------|-------|------|-------------|
| POST | `/api/profil` | candidat | Créer son profil |
| GET | `/api/profil` | candidat | Consulter son profil |
| PUT | `/api/profil` | candidat | Modifier son profil |

#### **Compétences (Candidat)**
| Méthode | Route | Rôle | Description |
|---------|-------|------|-------------|
| POST | `/api/profil/competences` | candidat | Ajouter une compétence |
| DELETE | `/api/profil/competences/{id}` | candidat | Supprimer une compétence |

#### **Candidatures (Candidat)**
| Méthode | Route | Rôle | Description |
|---------|-------|------|-------------|
| POST | `/api/offres/{offre}/candidater` | candidat | Postuler à une offre |
| GET | `/api/mes-candidatures` | candidat | Voir ses candidatures |

#### **Offres d'emploi (Recruteur)**
| Méthode | Route | Rôle | Description |
|---------|-------|------|-------------|
| POST | `/api/offres` | recruteur | Créer une offre |
| PUT | `/api/offres/{id}` | recruteur | Modifier une offre |
| DELETE | `/api/offres/{id}` | recruteur | Supprimer une offre |

#### **Candidatures (Recruteur)**
| Méthode | Route | Rôle | Description |
|---------|-------|------|-------------|
| GET | `/api/offres/{offre}/candidatures` | recruteur | Voir les candidatures à une offre |
| PATCH | `/api/candidatures/{id}/statut` | recruteur | Changer le statut d'une candidature |

#### **Administration (Admin)**
| Méthode | Route | Rôle | Description |
|---------|-------|------|-------------|
| GET | `/api/admin/users` | admin | Lister tous les utilisateurs |
| DELETE | `/api/admin/users/{id}` | admin | Supprimer un utilisateur |
| PATCH | `/api/admin/offres/{id}` | admin | Activer/Désactiver une offre |

---

##  Authentification

### **Type d'authentification: JWT (JSON Web Token)**

L'authentification utilise la librairie **php-open-source-saver/jwt-auth**.

### **Flux d'authentification:**

#### **1. Inscription (Register)**

```bash
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "role": "candidat"
}
```

**Réponse (201):**
```json
{
  "message": "Utilisateur créé avec succès",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "candidat"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

#### **2. Connexion (Login)**

```bash
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Réponse (200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "candidat"
  }
}
```

### **Utiliser le token:**

Ajouter l'en-tête `Authorization` à chaque requête protégée:

```bash
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```



##  Rôles et permissions

Le système comporte **3 rôles**:

| Rôle | Permissions |
|------|-------------|
| **candidat** | Créer un profil, postuler aux offres, voir ses candidatures, gérer compétences |
| **recruteur** | Créer/modifier/supprimer ses offres, voir les candidatures, changer statuts |
| **admin** | Gérer tous les utilisateurs et offres, suppression complète |

Le **middleware `CheckRole`** valide les permissions sur chaque route.

---

##  Collection Postman

Une collection Postman complète est fournie dans le dossier `postman/`.

### **Importer la collection:**

1. Ouvrir **Postman**
2. Cliquer sur **Import** → **File**
3. Sélectionner `postman/Mini_LinkedIn_Collection.json`
4. Importer également l'environnement: `postman/environment.json`

### **Variables d'environnement:**

- `base_url` : URL de base (ex: `http://localhost:8000`)
- `token` : Token JWT (sera rempli après login)
- `candidat_token` : Token d'un candidat
- `recruteur_token` : Token d'un recruteur

### **Voir la documentation complète:** `postman/README.md`



##  Base de données

### **Tables principales:**

#### **users**
| Colonne | Type | Contraintes |
|---------|------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | UNIQUE, NOT NULL |
| password | VARCHAR(255) | NOT NULL |
| role | ENUM('candidat','recruteur','admin') | NOT NULL |
| email_verified_at | TIMESTAMP | NULLABLE |
| remember_token | VARCHAR(100) | NULLABLE |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |

#### **profils**
| Colonne | Type | Contraintes |
|---------|------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT |
| user_id | INT | FOREIGN KEY → users.id |
| titre | VARCHAR(255) | NOT NULL |
| bio | TEXT | NULLABLE |
| localisation | VARCHAR(255) | NOT NULL |
| disponible | BOOLEAN | DEFAULT TRUE |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |

#### **offres**
| Colonne | Type | Contraintes |
|---------|------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT |
| user_id | INT | FOREIGN KEY → users.id |
| titre | VARCHAR(255) | NOT NULL |
| description | TEXT | NOT NULL |
| localisation | VARCHAR(255) | NOT NULL |
| type | ENUM('CDI','CDD','Stage') | NOT NULL |
| actif | BOOLEAN | DEFAULT TRUE |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |

#### **candidatures**
| Colonne | Type | Contraintes |
|---------|------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT |
| profil_id | INT | FOREIGN KEY → profils.id |
| offre_id | INT | FOREIGN KEY → offres.id |
| message | TEXT | NULLABLE |
| statut | ENUM('en_attente','acceptee','refusee') | DEFAULT 'en_attente' |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |

#### **competences**
| Colonne | Type | Contraintes |
|---------|------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT |
| nom | VARCHAR(255) | NOT NULL |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |

#### **profil_competence** (Pivot)
| Colonne | Type | Contraintes |
|---------|------|-------------|
| profil_id | INT | FOREIGN KEY → profils.id |
| competence_id | INT | FOREIGN KEY → competences.id |
| niveau | ENUM('débutant','intermédiaire','expert') | NOT NULL |



##  Événements

Le projet utilise le système d'**événements Laravel**:

### **CandidatureDeposee**
Déclenché quand un candidat postule à une offre.
- Envoie une notification au recruteur
- Enregistre l'action dans les logs

### **StatutCandidatureMis**
Déclenché quand le statut d'une candidature change.
- Envoie une notification au candidat
- Met à jour les statistiques



##  Gestion des erreurs

L'API retourne des codes HTTP standard:

| Code | Signification | Exemple |
|------|---------------|---------|
| **200** | OK | Requête réussie |
| **201** | Created | Ressource créée |
| **204** | No Content | Suppression réussie |
| **400** | Bad Request | Erreur dans la requête |
| **401** | Unauthorized | Token invalide/expiré |
| **403** | Forbidden | Accès interdit (rôle insuffisant) |
| **404** | Not Found | Ressource non trouvée |
| **422** | Unprocessable Entity | Validation échouée |
| **500** | Internal Server Error | Erreur serveur |

### **Format d'erreur:**
```json
{
  "message": "Validation échouée",
  "errors": {
    "email": ["L'email doit être unique"],
    "password": ["Le mot de passe doit contenir au moins 6 caractères"]
  }
}
```



##  Tests avec Postman

### **Scénario complet de test:**

1. **Register:** Créer un compte candidat
2. **Login:** Se connecter
3. **Create Profil:** Créer un profil
4. **Add Competence:** Ajouter des compétences
5. **List Offres:** Consulter les offres
6. **Candidater:** Postuler à une offre
7. **Mes Candidatures:** Voir ses candidatures

### **Tests d'erreur obligatoires:**

-  401 Unauthorized (sans token)
-  403 Forbidden (rôle incorrect)
-  404 Not Found (ressource inexistante)
-  422 Unprocessable (validation échouée)





##  Licence

Ce projet est sous licence **MIT**.



##  Auteur

**Sara Ben Rais et ikram lamhamdi**  
GitHub: [@benraissara](https://github.com/benraissara)



##  Support

Pour toute question ou problème, ouvrir une **Issue** sur GitHub.

---

##  Ressources

- [Documentation Laravel 12](https://laravel.com/docs/12.x)
- [JWT Auth Laravel](https://github.com/tymondesigns/jwt-auth)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Postman Learning Center](https://learning.postman.com/)

---

**Dernière mise à jour:** 20 avril 2026


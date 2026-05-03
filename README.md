# rest_mediatekdocuments

> Ce dépôt est une évolution du projet d'origine disponible ici :
> **https://github.com/CNED-SLAM/rest_mediatekdocuments**
> Le README de ce dépôt présente la structure complète de l'API d'origine ainsi que son mode d'exploitation.

API REST PHP permettant d'accéder à la base de données MySQL `mediatek86` d'un réseau de médiathèques. Elle est consommée par l'application desktop C# MediatekDocuments.

---

## Fonctionnalités ajoutées

### Authentification renforcée
Les identifiants d'accès à l'API ont été sécurisés. Les nouveaux identifiants sont stockés dans le fichier `.env` (non versionné) :

- **Authentification** : Basic Auth
- **Login** : `mediatekapi`
- **Mot de passe** : stocké dans `.env` (non communiqué publiquement)

### Route utilisateur
Une nouvelle route `utilisateur` a été ajoutée dans `MyAccessBDD.php` pour permettre à l'application C# de vérifier les identifiants de connexion et de récupérer le service de l'utilisateur.

### Déploiement en ligne
L'API est déployée et accessible en ligne sur Alwaysdata :

**URL de l'API :** https://orianecned.alwaysdata.net/api/

### Documentation technique
La documentation technique du code PHP a été générée avec phpDocumentor et est accessible en ligne :
https://orianecned.alwaysdata.net/doc_rest_mediatekdocuments/index.html

---

## Captures d'écran

### Test de l'API avec Postman — GET livre (200 OK)
*(insérer capture Postman)*

---

## Mode opératoire — Installation et utilisation en local

### Prérequis
- WampServer ou XAMPP
- NetBeans (ou équivalent) pour éditer le code PHP
- Composer pour installer les dépendances
- Postman pour tester l'API

### Installation

**1. Cloner le dépôt**
```
git clone https://github.com/orient75015/rest_mediatekdocuments.git
```
Placer le dossier dans `www` (WampServer) ou `htdocs` (XAMPP) et le renommer en `rest_mediatekdocuments`.

**2. Installer les dépendances via Composer**
Ouvrir une fenêtre de commandes en mode administrateur dans le dossier `src` et exécuter :
```
composer install
```

**3. Créer la base de données**
Avec phpMyAdmin, créer une base de données `mediatek86` et importer le script `mediatek86.sql` situé à la racine du projet.

**4. Configurer le fichier .env**
Créer un fichier `.env` dans le dossier `src` avec les informations suivantes :
```
AUTHENTIFICATION=basic
AUTH_USER=admin
AUTH_PW=adminpwd
BDD_LOGIN=root
BDD_PWD=
BDD_BD=mediatek86
BDD_SERVER=localhost
BDD_PORT=3306
```

**5. Tester l'API avec Postman**
- URL : `http://localhost/rest_mediatekdocuments/livre`
- Méthode : GET
- Authentification : Basic Auth, Username `admin`, Password `adminpwd`

---

## Exploitation de l'API

### Adresses
- **En local :** `http://localhost/rest_mediatekdocuments/`
- **En ligne :** `https://orianecned.alwaysdata.net/api/`

### Requêtes disponibles

| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/livre` | Liste des livres |
| GET | `/dvd` | Liste des DVD |
| GET | `/revue` | Liste des revues |
| GET | `/genre` | Liste des genres |
| GET | `/public` | Liste des publics |
| GET | `/rayon` | Liste des rayons |
| GET | `/etat` | Liste des états |
| GET | `/suivi` | Liste des suivis |
| GET | `/exemplaire/{"id":"..."}` | Exemplaires d'une revue |
| GET | `/commandedocument/{"id":"..."}` | Commandes d'un document |
| GET | `/abonnement/{"id":"..."}` | Abonnements d'une revue |
| GET | `/utilisateur/{"login":"..."}` | Données d'un utilisateur |
| POST | `/commande` | Ajouter une commande |
| PUT | `/commandedocument/id` | Modifier une commande |
| DELETE | `/commande/{"id":"..."}` | Supprimer une commande |
| POST | `/abonnement` | Ajouter un abonnement |
| DELETE | `/abonnement/{"id":"..."}` | Supprimer un abonnement |
| POST | `/exemplaire` | Ajouter un exemplaire |


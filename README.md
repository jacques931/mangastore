# MangaStore 📚

Application e-commerce pour la vente de mangas, figurines et autres produits développée avec Symfony.

> **Note :** Ce projet a été réalisé dans le cadre d'un projet scolaire pour démontrer mes compétences techniques et pratiques en développement web.

## Fonctionnalités 🚀

### Espace Client 👤

#### Gestion du Compte
- Inscription avec :
  - Nom
  - Email
  - Mot de passe sécurisé
  - Adresse de livraison
- Connexion sécurisée
- Modification du profil
- Historique des commandes

#### Navigation et Achats
- Parcours des catégories
- Visualisation détaillée des produits
- Système de panier en session avec :
  - Ajout de produits
  - Modification des quantités
  - Suppression de produits
- Processus de commande avec :
  - Récapitulatif détaillé
  - Prix par article
  - Montant total
  - Frais de livraison

### Espace Administrateur 🔐

#### Gestion du Catalogue
- Gestion des catégories :
  - Création
  - Modification
  - Suppression
- Gestion des produits :
  - Ajout avec image
  - Modification
  - Suppression
  - Attribution à une catégorie

#### Suivi des Commandes
- Vue d'ensemble des commandes
- Détails par commande :
  - Numéro de commande
  - Informations client
  - Articles commandés
  - Montants
  - Statut

#### Gestion des Utilisateurs
- Liste des utilisateurs
- Modification des droits
- Suppression de compte

## Sécurité 🔒

- Authentification sécurisée
- Hachage des mots de passe
- Protection CSRF
- Gestion des rôles (ROLE_USER, ROLE_ADMIN)
- Sessions sécurisées

## Technologies Utilisées 💻

- Symfony 7
- Doctrine ORM
- Twig
- Bootstrap 5
- FontAwesome
- MySQL

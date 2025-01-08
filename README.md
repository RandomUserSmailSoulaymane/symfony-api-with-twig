## Symfony API Project

# Description du Projet

Ce projet implémente une API REST en Symfony, gérant plusieurs entités principales — dont Users, Articles et Products. Il inclut :

  CRUD (Create, Read, Update, Delete) complet pour chaque entité.
  Authentification via JSON Web Tokens (JWT) pour sécuriser les endpoints.
  Documentation & Tests gérés avec Postman.

L’objectif est de démontrer la mise en œuvre d’une API Symfony professionnelle, avec des bonnes pratiques Git et une gestion automatique de la documentation.


# Comment Exécuter le Projet Localement

  1. Cloner le dépôt :

    git clone https://github.com/YourUsername/YourRepoName.git
    cd YourRepoName

  2. Installer les dépendances :

    composer install

  Assurez-vous d’avoir Composer et PHP (version 8 ou plus) installés.

  3. Configurer la base de données :

  . Mettez à jour le fichier .env ou .env.local avec vos identifiants (ex. DATABASE_URL).
  . Créez la base de données :

    symfony console doctrine:database:create
    symfony console doctrine:migrations:migrate

  4. Générer les clés JWT si votre application utilise LexikJWTAuthenticationBundle :

    mkdir config/jwt
    openssl genrsa -out config/jwt/private.pem -aes256 4096
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    # Mettre la passphrase dans .env.local => JWT_PASSPHRASE=...
    # dans le repo c'est mon nom en maj.

   Vérifiez que config/packages/lexik_jwt_authentication.yaml est configuré.

   5. Lancer le serveur de développement :

    symfony serve

ou

    php -S 127.0.0.1:8000 -t public

  Ouvrez http://127.0.0.1:8000 dans votre navigateur.

# Endpoints Principaux

    NB: Tous les endpoints (sauf ceux de registration/login) exigent un token Bearer <JWT> dans l’en-tête Authorization.

# Utilisateurs (Users)

    POST /api/users
  Créer un nouvel utilisateur (registration).
    
    POST /api/login
  Authentifier un utilisateur et obtenir un token JWT.
    
    GET /api/users
  Lister tous les utilisateurs (exemple, si ROLE_ADMIN le permet).
    
    GET /api/users/{id}
  Obtenir un utilisateur précis.
    
    PUT ou PATCH /api/users/{id}
  Mettre à jour les infos d’un utilisateur (email, password, etc.).
    
    DELETE /api/users/{id}
  Supprimer un utilisateur.

# Articles

    GET /api/articles
  Lister tous les articles.
    
    POST /api/articles
  Créer un nouvel article.
    
    GET /api/articles/{id}
  Obtenir un article précis.
    
    PUT ou PATCH /api/articles/{id}
  Mettre à jour un article.
    
    DELETE /api/articles/{id}
  Supprimer un article.

# Produits (Products)

    GET /api/products
  Lister tous les produits.
    
    POST /api/products
  Créer un nouveau produit.
    
    GET /api/products/{id}
  Obtenir un produit précis.
    
    PUT ou PATCH /api/products/{id}
  Mettre à jour un produit.
    
    DELETE /api/products/{id}
  Supprimer un produit.

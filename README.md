# panier-ecommerce-symfony
Panier e-commerce Symfony avec envoi de la commande par mail

## Environnement de développement

### Pré-requis

* PHP 7.4
* MySQL
* Composer
* Symfony CLI

Vous pouvez vérifier les pré-requis avec la commande suivante (issue de la Symfony CLI) :

```bash
symfony check:requirements
```

### Configuration de l'environnement de développement

cloner le repo Github
```bash 
git clone https://github.com/citizenz7/panier-ecommerce-symfony.git
```

se placer dans le nouveau répertoire
```bash 
cd panier-ecommerce-symfony
```

configurer le fichier .env
* MySQL
* Mail

Installer les dépendances
```bash 
composer install
```

créer la base de données MySQL
```bash 
symfony serve d:d:c
```

importer la migration
```bash 
symfony serve d:m:m
```

### Lancement de l'environnement de développement

Démarrer le serveur de développement Symfony
```bash
symfony serve -d
```

Rendez-vous sur 127.0.0.1:8000
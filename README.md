📄 Contenu proposé pour README.md de Sporthive

# 🏋️ Sporthive

Sporthive est une plateforme web conçue pour **connecter les gens à travers le sport**. Que vous voyagiez, découvriez une nouvelle ville ou cherchiez simplement des partenaires de jeu, Sporthive vous permet de rejoindre des activités sportives avec des personnes partageant les mêmes intérêts — transformant de simples parties en souvenirs durables.

## 🚀 Fonctionnalités

- **Gestion des Compétitions et Tournois** : Création et gestion d'événements sportifs
- **Système de Paiement Sécurisé** : Intégration de Stripe pour le paiement des assurances sportives
- **Notifications SMS** : Système d'alertes SMS via Twilio pour les réclamations
- **Modération de Contenu** : Filtrage automatique des contenus inappropriés via l'API PurgoMalum
- **Assurances Sportives** : Système complet de gestion des assurances et réclamations

## 🛠️ Technologies Utilisées

- **Backend** : Symfony (PHP)
- **Frontend** : HTML, CSS, JavaScript, Twig
- **Base de données** : MySQL/MariaDB
- **APIs** :
  - Stripe (Paiements)
  - Twilio (SMS)
  - PurgoMalum (Modération)

## ⚙️ Installation

1. Clonez le repository
```bash
git clone https://github.com/ademgabsi/sporthive.git
cd sporthive
```

2. Installez les dépendances
```bash
composer install
npm install
```

3. Configurez votre fichier .env.local avec les variables suivantes :
```env
# Base de données
DATABASE_URL=mysql://user:password@127.0.0.1:3306/sporthive

# Stripe
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...

# Twilio
TWILIO_SID=AC853...
TWILIO_TOKEN=9b1cf...
TWILIO_FROM=+1620...
```

4. Créez la base de données et effectuez les migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Lancez le serveur
```bash
symfony server:start
```

## 🔒 Sécurité

- Les clés API sont stockées de manière sécurisée dans le fichier .env.local
- Le système de paiement utilise Stripe Checkout pour une sécurité maximale
- Validation et filtrage des données utilisateur via PurgoMalum

## 🤝 Contribuer

Les contributions sont les bienvenues ! Voici comment faire :

1. Fork le projet
2. Créez une branche : `git checkout -b ma-fonctionnalité`
3. Committez vos changements : `git commit -m "Ajoute une fonctionnalité"`
4. Pushez sur la branche : `git push origin ma-fonctionnalité`
5. Ouvrez une Pull Request

## 📝 License

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.
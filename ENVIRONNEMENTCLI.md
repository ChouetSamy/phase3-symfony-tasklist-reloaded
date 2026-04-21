installer, initialiser et lancer le compilateur tailwind

composer require symfonycasts/tailwind-bundle #instalation
php bin/console tailwind:init #initialisation avant compilation
php bin/console tailwind:build --watch #compilation, permet de voir les modification en temps réel

installer le bundle Security, pour sécurisé l'authentifaction et la connexion, puis paramètrer la sécurité

composer require symfony/security-bundle #installation
php bin/console make:user #creation de l'user, crud, formulaire, validator, connexion

php bin/console make:migration #creation du sql User
php bin/console doctrine:migrations:migrate # execute le sql User

composer require symfonycasts/verify-email-bundle #bundle pour verifié la validité de l'email
php bin/console make:registration-form #créer le formulaire d'enregistrement

composer require symfony/rate-limiter #limit les attaques brute force sur la connexion

php bin/console make:security:form-login #créer un formulaire de login


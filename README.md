ApiJobs
=======


Installer wamp (si vous ne bénficiez pas de plateforme de développement web).
Enregistrer les variables d'environnement mysql et php si besoin

Récupérez le projet en faisant un Git clone ou téléchargez-le depuis la page projet de mon compte Github appelé "ApiJobs"
git clone https://github.com/MaxQuero/JobsApi

Installez symfony de la manière suivante :
  Linux / Mac :
  sudo curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony
  sudo chmod a+x /usr/local/bin/symfony


  Windows :
  php -r "readfile('https://symfony.com/installer');" > symfony

  Déplacer le fichier nommé "symfony" fraîcheemnt créé soit dans le dossier englobé par la variable d'environnement, soit dans votre dossiers "projects" (on devra alors utiliser la commande "php symfony" et non simplement "symfony")

  Si les certificats SSL ne sont pas correctement installés sur vote machine, vous risquez d'avoir une erreur de la sorte : "cURL error 60: SSL certificate problem: unable to get local issuer certificate".
  Si c'est le cas, télécharger le fichier contenant la liste des certificats à jour à l'adresse suivante : https://curl.haxx.se/ca/cacert.pem et déplacer le à un endroit sûr.
  Modifiez le fichier php.ini de la sorte : 
  ; Linux and macOS systems
  curl.cainfo = "/path/to/cacert.pem"

  ; Windows systems
  curl.cainfo = "C:\path\to\cacert.pem"

Faites un composer install dans le dossier du projet pour installer les dépendances, telles que le restBundle.
Créez la base de donnée "digital_garden" grâce à la commande suivante : 'php bin/console doctrine:database:create'
Mettez à jour la base de données et créez les tables grâce aux entity créés dans Symfony grâce à la commande suivante : "php bin/console doctrine:schema:update --force"

Vous avez maintenant accès à l'application.
rendez-vous à l'adresse /jobs pour récupérer l'ensemble des dernières annonces, et, suite à ça, à l'adresse /api/jobs pour accéder à l'API Rest retournant les jobs en format JSON.
A Symfony project created on May 17, 2017, 9:17 am.

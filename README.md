ApiJobs
=======


Installer wamp (si vous ne bénficiez pas de plateforme de développement web).
Enregistrer les variables d'environnement mysql et php si besoin

Télécharger et installer composer : https://getcomposer.org/.

Récupérez le projet JobsApi en faisant un Git clone (git clone https://github.com/MaxQuero/JobsApi
) ou téléchargez-le depuis la page projet de mon compte Github (https://github.com/MaxQuero/JobsApi)

Faites un composer install dans le dossier du projet pour installer les dépendances, telles que le restBundle.

Créez la base de données "digital_garden" grâce à la commande suivante : 'php bin/console doctrine:database:create' (attention a bien modifié le nom lorsque celui-ci est demandé).

Mettez à jour la base de données et créez les tables grâce aux entity créés dans Symfony grâce à la commande suivante : "php bin/console doctrine:schema:update --force"

Vous avez maintenant accès à l'application.

Rendez-vous à l'adresse "/jobs" pour récupérer l'ensemble des dernières annonces, et, suite à ça, à l'adresse "/api/jobs" pour accéder à l'API Rest retournant les jobs en format JSON.

A Symfony project created on May 17, 2017, 9:17 am.

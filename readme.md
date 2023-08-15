Pré requis :
- serveur web installé en local avec php 8.1, mySql
- avoir installé composer en global : https://getcomposer.org/download/
- avoir installé NPM en global : https://www.npmjs.com/package/npm-install-global
- avoir installé la CLI de Symfony : https://symfony.com/download

Pour dupliquer le projet :

- avec la console se positionner dans le dossier de projets (cd /path/to/root/folder)
<pre><code>git clone https://github.com/ComN-Stay/www.legion.org.git</code></pre>
<pre><code>composer update</code></pre>
<pre><code>npm install</code></pre>
- créer à la racine du dossier de projet un fichier .env.local
- copier/coller le bloc ci dessous et modifier les paramètres de la ligne DATABASE_URL
<pre><code>
###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=2aaf6054be383cd87ab04629b59d2a71
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://DBUSER:DBPASSWORD@127.0.0.1:3306/DBNAME?serverVersion=8.0.32&charset=utf8mb4"
###< doctrine/doctrine-bundle ###

###> symfony/messenger ###
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
###< symfony/messenger ###

###> symfony/mailer ###
MAILER_DSN=smtp://localhost:1025
MAILER_SENDER=contact@legion.local
MAILER_FROM='Légion <contact@legion.local>'
###< symfony/mailer ###
</code></pre>
- Dans la console :
création de la BDD en local
<pre><code>php bin/console doctrine:database:create</code></pre>
<pre><code>php bin/console make:migration</code></pre>
<pre><code>php bin/console doctrine:migration:migrate</code></pre>
<pre><code>php bin/console doctrine:fixtures:load</code></pre>

lancement du serveur de symfony
<pre><code>symfony server:start -d</code></pre>

Pour pouvoir tester les envois de mail :
- installer Mailcatcher en local : https://mailcatcher.me/
- modifier le php.ini : rechercher, décommenter et modifier la ligne "sendmail_path" et mettre sendmail_path = /usr/bin/env catchmail -f contact@legion.local
Pré requis :
    - serveur web installé en local avec php 8.1, mySql
    - avoir installé composer en global
    - avoir installé NPM en global

Pour dupliquer le projet :

- créer un dossier sur votre machine et se positionner dedans avec la console
- dans la console : git clone https://github.com/ComN-Stay/www.legion.org.git
- dans la console : composer update
- créer à la racine un fichier .env.local
- copier/coller le bloc ci dessous et modifier les paramètres de la ligne DATABASE_URL

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=2aaf6054be383cd87ab04629b59d2a71
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
DATABASE_URL="mysql://DBUSER:DBPASSWORD@127.0.0.1:3306/DBNAME?serverVersion=8.0.32&charset=utf8mb4"
###< doctrine/doctrine-bundle ###

###> symfony/messenger ###
# Choose one of the transports below
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
###< symfony/messenger ###

###> symfony/mailer ###
# MAILER_DSN=null://null
###< symfony/mailer ###

- Dans la console : php bin/console d:d:c (création de la BDD en local)
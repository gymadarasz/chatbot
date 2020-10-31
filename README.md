clone webapp and chatbot next ot each other into your www folder:
```
git clone https://github.com/gymadarasz/webapp.git
git clone https://github.com/gymadarasz/chatbot.git
```
in the result two folder here:
```
|
+-- webapp
+-- chatbot
```

install:
```
cd chatbot
composer install
```

config: (you can add more)
```
cd ../webapp
cp src/config/config.php.dist src/config/config.dev.php
cp src/config/config.php.dist src/config/config.test.php
cp src/config/config.php.dist src/config/config.live.php

cp src/config/config.php.dist ../chatbot/src/config/config.dev.php
cp src/config/config.php.dist ../chatbot/src/config/config.test.php
cp src/config/config.php.dist ../chatbot/src/config/config.live.php
```
Set enviroment in `src/config/env.php` to one of the following: `dev`, `test`, `live`

Set the proper values in files: 

    `webapp/src/config/config.dev.php` <- for local developement
    
    `webapp/src/config/config.test.php` <- for testing
    
    `webapp/src/config/config.live.php` <- for live

    `chatbot/src/config/config.dev.php` <- config overrides for local developement in project extension
    
    `chatbot/src/config/config.test.php` <- config overrides for testing in project extension
    
    `chatbot/src/config/config.live.php` <- config overrides for live in project extension
    

DB:
see in `webapp.sql`

tail monitoring: (path maybe different on your local environment)
```
tail -f /var/log/apache2/error.log -f /var/www/webapp/logs/*.log -f /var/log/apache2/access.log
```

tests:
```
cd ../chatbot
./test.sh
```

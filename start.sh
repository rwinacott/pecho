#!/bin/bash
#
#php -S 0.0.0.0:8888 -t /tmp echo.php

docker run -p 8888:8888 -it --rm --name php-echo-server -v "$PWD":/usr/src/myapp -w /usr/src/myapp php:7.0-cli php -S 0.0.0.0:8888 -t /tmp echo.php

docker run -p 8888:8888 -it --rm --name echo-server sk-php-echo-server-app

docker build -t sk-php-echo-server-app .

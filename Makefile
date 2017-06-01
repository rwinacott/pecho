all:
	cp -f echo.php docker/echo.php
	cd docker
	docker build -t sk-php-echo-server-app ./docker
	
	

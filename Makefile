all:
	cp -f echo.php docker/echo.php
	cd docker
	docker build -t pecho-server ./docker
	
	

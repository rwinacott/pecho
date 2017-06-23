all:
	cp -f echo.php docker/echo.php
	cd docker && docker build -t securekey/pecho-server:sdk .
	
run:
	docker run -p 8888:8888 -it --rm --name pecho-server securekey/pecho-server:sdk


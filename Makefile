all:
	cp -f echo.php docker/echo.php
	cd docker
	docker build -t pecho-server ./docker
	
run:
    docker run -p 8888:8888 -it --rm --name pecho-server pecho-server


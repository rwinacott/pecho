# Overview

The pecho service is a simple web application running inside a docker container that will simulate a downstream system of the Matching Service.

The web server used is the built in PHP server. This is only used for test cases, test runs, etc. The PHP build-in http server is not suitable for production systems. See the `docker/Dockerfile` for more information.

There is no "compiling" of the code, but you do have to build the docker image. To do that, run the `make` command. This will copy the pecho.php file in to the docker folder and call the `docker build -t pecho-server ./docker` command.

When you run the `make` command you will see the following output as the docker images is being built.

```bash
$ make
cp -f echo.php docker/echo.php
cd docker
docker build -t pecho-server ./docker
Sending build context to Docker daemon 4.608 kB
Step 1/6 : FROM php:5.6-cli
 ---> a60207530b31
Step 2/6 : EXPOSE 8888:8888
 ---> Using cache
 ---> 305da7ebea0e
Step 3/6 : RUN mkdir -p /usr/src/myapp
 ---> Using cache
 ---> a963770a6bf5
Step 4/6 : COPY echo.php /usr/src/myapp
 ---> Using cache
 ---> 0c90521d5775
Step 5/6 : WORKDIR /usr/src/myapp
 ---> Using cache
 ---> 4f23e30aa4f4
Step 6/6 : CMD php -S 0.0.0.0:8888 -t /usr/src/myapp echo.php
 ---> Using cache
 ---> 1f691c64eac0
Successfully built 1f691c64eac0
$
```
Now you can run the `docker images` command to see the list of local images you have. You should be able to see the `pecho-server` image:

```bash
$ docker images
REPOSITORY               TAG                 IMAGE ID            SIZE
pecho-server             latest              1f691c64eac0        356 MB
```

## No Make command

To do what the `make` command will do, run the following:
```bash
$ cp -f echo.php docker/echo.php
$ cd docker
$ docker build -t pecho-server .
```

This will build the local docker image tagged `pecho-server` on your system.

## Running the service

To run the docker image, you need to create a `container` from the image you just created. The application would like to use port 8888 to take requests on. Only `POST` requests are supported. All other types including `GET` will return an error.

There are *no* volumes configured in the application. No need for them.

To start (run) the container use;

```bash
$ docker run -it -d -p 8888:8888 --name pecho-server pecho-server
07edb45ebb9f5713d8fc4668d78e356be933907f393b33517a8f734a1c47551a
$
```

You can run the docker container in the foreground for testing a change with the following command. The container will be removed once it is stopped. (see the --rm option)

```bash
$ docker run -it --rm -p 8888:8888 --name pecho-server pecho-server
```

If you run the foreground docker container, it will leave old (dead) images in the Docker environment. To cleanup all of the old versions created using the `make` command or `docker build` commands above, you should run the following command pipeline;

```bash
$ docker images -q --filter "dangling=true" | xargs docker rmi
```

If you see an error message about the port already bound, you need to pick a different local port number instead of the 8888 port. Change the first port number to something else like:

```bash
$ docker run -it -d -p 8888:8888 --name pecho-server pecho-server
e493b7fde2d1316afa996d4423dc5055243daa078b6d0052f754282977192b6b
docker: Error response from daemon: driver failed programming external connectivity on endpoint pecho-server (c530b6af25d06c9495b51d01f00f3772a950584e43d2597e005a05a90d03272d): Bind for 0.0.0.0:8888 failed: port is already allocated.
$ docker rm e493b7fde2d1316afa996d4423dc5055243daa078b6d0052f754282977192b6b
$ docker run -it -d -p 8555:8888 --name pecho-server pecho-server
8567c67275e5c0036fd0c9c973bf6f14713b669e45608e4917f5a19edea89b12
```

*note:* you should delete (remove) the dead container from your system. See the `docker rm <container ID>` command in the example above.

You should be able to see the running container now with the following command;

```bash
$ docker ps
NAMES                    CONTAINER ID        IMAGE                           STATUS              PORTS
pecho-server             8567c67275e5        pecho-server                    Up 4 minutes        0.0.0.0:8888->8888/tcp
```

If you already have the container created, you can start it using;

```bash
$ docker start -it -d -p 8888:8888 <container-id/Name>
````

Now you can configure the `mapper` side of the _matching server_ to call in to the pecho service for testing the `REST` interface.


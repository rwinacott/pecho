# pecho-server

This is a simple Digital Identity Matching Service (DIMS) downstream service that can be used for testing the **downstream REST interface** and as an example of the data structure exchange between a downstream system and DIMS.

The server is made up of a simple, single `echo.php` file that is run either as a standalone php cli command using the php built-in http server, or as a simple Docker container.

The server will use **port 8888** to take downstream requests on.

## Build

To build the Docker container, you require GNU Make installed to run the make command against the `Makefile`.

The default (all) Make target will perform the following steps to build the Docker Image.

1. Copy the echo.php file in to the `./docker` folder.
2. Change directly in to the `./docker` folder.
3. Run the `docker build -t securekey/echo-server:sdk .` command 

This make target will build the `SecureKey/echo-service:sdk` image in to your local Docker image repository.

You can now run the `docker images` command to see the results:

```bash
$ docker images
REPOSITORY              TAG  IMAGE ID      SIZE
securekey/pecho-server  sdk  30ad35f817e7  356 MB
```

## Running

You can run the server directly or as a docker container.

### Directly

To run the echo-service you can start it directly (no need to build the docker image) if you have php-5.6 or higher installed on your host OS.

The built-in server is used to keep the service running and answering requests.

```bash
$ php -S 0.0.0.0:8888 -t /tmp ./echo.php
```

The above command will start the server in the foreground. To stop it, press `^C` in the terminal.

### Docker container

Once you have the Docker Image built, you can start the container by hand or use the `run` Make target to start the container for you.

```bash
% make run
```

If you want a bit more control, you can start the docker container by using the command;

```bash
% docker run -p 8888:8888 -it --rm --name echo-server securekey/pecho-server:sdk
```

The Make target and the `run` command above will create the container and run it. Once the application exits, the container will be removed (see **--rm** for details) to cleanup behind itself.

## Downstream REST applications


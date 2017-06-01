FROM php:5.6-cli
EXPOSE 8888:8888
RUN mkdir -p /usr/src/myapp
COPY echo.php /usr/src/myapp
WORKDIR /usr/src/myapp
CMD [ "php", "-S", "0.0.0.0:8888", "-t", "/usr/src/myapp", "echo.php" ]

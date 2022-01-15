FROM webdevops/php-apache:7.4
WORKDIR /app/api
COPY . .
RUN chmod -R 777 /app
RUN chown -R application:application /app
EXPOSE 80

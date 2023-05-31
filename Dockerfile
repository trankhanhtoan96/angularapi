FROM webdevops/php-apache:7.4-alpine
WORKDIR /app/api
COPY --chown=apache:apache ./api /app/api

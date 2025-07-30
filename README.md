FROM php:8.1-cli

RUN apt-get update && apt-get install -y curl unzip

COPY . /app
WORKDIR /app

CMD ["php", "-S", "0.0.0.0:80", "index.php"]

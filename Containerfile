# abraflexi-pricefixer

FROM php:8.2-cli
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions gettext intl zip imap
COPY src /usr/src/abraflexi-pricefixer/src
RUN sed -i -e 's/..\/.env//' /usr/src/abraflexi-pricefixer/src/*.php
COPY composer.json /usr/src/abraflexi-pricefixer
WORKDIR /usr/src/abraflexi-pricefixer
RUN curl -s https://getcomposer.org/installer | php
RUN ./composer.phar install
WORKDIR /usr/src/abraflexi-pricefixer/src
CMD [ "php", "./fixall.php" ]

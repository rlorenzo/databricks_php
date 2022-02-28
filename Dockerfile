FROM --platform=linux/amd64 php:7.4-cli

RUN apt-get update \
    && apt-get install wget unzip git unixodbc unixodbc-dev libpq-dev -y

# Found this stuff here: https://github.com/docker-library/php/issues/103
# This gets rid of ODBC errors at the top of the page in Moodle, but doesn't automatically
# fix the registrar connection

RUN set -x \
    && docker-php-source extract \
    && cd /usr/src/php/ext/odbc \
    && phpize \
    && sed -ri 's@^ *test +"\$PHP_.*" *= *"no" *&& *PHP_.*=yes *$@#&@g' configure \
    && ./configure --with-unixODBC=shared,/usr \
    && docker-php-ext-install odbc \
    && docker-php-source delete

# Quite sure this stuff is necessary - it just installs a bunch of php pdo extensions
RUN set -x \
    && apt-get install -y unixodbc unixodbc-dev freetds-dev freetds-bin tdsodbc \
    && if ! [ -h /usr/lib/libsybdb.a ]; then ln -s /usr/lib/x86_64-linux-gnu/libsybdb.a /usr/lib/; fi \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_dblib \
    && docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \
    && docker-php-ext-install pdo_odbc

# Much of the following code was adapted from https://hub.docker.com/r/stephenbutcher/mar10c7/~/dockerfile/

# Install the Databricks ODBC Driver
RUN apt-get install libsasl2-modules-gssapi-mit -y
RUN wget https://databricks-bi-artifacts.s3.us-east-2.amazonaws.com/simbaspark-drivers/odbc/2.6.19/SimbaSparkODBC-2.6.19.1033-Debian-64bit.zip \
    && unzip SimbaSparkODBC-2.6.19.1033-Debian-64bit.zip \
    && dpkg -i simbaspark_2.6.19.1033-2_amd64.deb

# Copy odbc.ini file to the correct location
ADD odbc.ini /etc/odbc.ini

COPY config.php /
COPY test_connection.php /
CMD [ "php", "./test_connection.php" ]

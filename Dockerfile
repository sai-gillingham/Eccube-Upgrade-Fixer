FROM php:7.1-cli

MAINTAINER Kiy0taka Oku

ADD build/eccube-upgrade-fixer.phar /usr/local/bin/eccube-upgrade-fixer

ENTRYPOINT ["eccube-upgrade-fixer"]

#!/bin/bash
#
# Запросить данные по звонкам на начало текущего часа
# Сохранить отчет в файл
# Отправить отчет в Телеграмм
#

php -c ~/etc/php.ini get.php > calls.txt
php -c ~/etc/php.ini send.php

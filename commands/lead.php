<?php
use \AmoCRM\Handler;
use \AmoCRM\Request;

require_once __DIR__ . '/../vendor/autoload.php';

/* Создание экземпляра API, где "domain" - имя вашего домена в AmoCRM, а
"user@example.com" - email пользователя, от чьего имени будут совершаться запросы */
$api = new Handler('new4', 'fedor@neq4.ru');

/* Создание экземляра запроса */

/* Вторым параметром можно передать дополнительные параметры поиска (смотрите в документации)
В этом примере мы ищем пользователя с номером телефона +7 916 111-11-11
Чтобы получить полный список, укажите пустой массив []
Третьим параметром указывается метод в формате [название объекта, метод] */
$request = new Request(Request::GET, [], ['leads', 'list']);

/* Выполнение запроса */
$result = (array) $api->request($request)->result;

print_r(compact('result'));

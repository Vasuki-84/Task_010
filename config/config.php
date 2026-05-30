<?php

date_default_timezone_set('Asia/Kolkata');

$env = parse_ini_file(
    __DIR__ . '/../.env'
);

foreach ($env as $key => $value) {

    $_ENV[$key] = $value;
}
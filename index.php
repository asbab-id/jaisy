<?php
// php -S localhost:45646 index.php

// struktur fw /////
// index.php (route)
// phprouter.php (route vendor)
// .htaccess (apache settings)
// app/
// page/
// static/
// vendor/


require_once __DIR__.'/phprouter.php';



get('/', 'app/jaisy/playground.php');
post('/interpreter', 'app/jaisy/interpreter.php');
get('/char', 'app/jaisy/playground-char.php');

get('/docs', 'app/jaisy/docs/index.php');
get('/docs/$versi', 'app/jaisy/docs/index.php');

get('/tmp', 'app/jaisy/tmp.php');
get('/tmp-php', 'app/jaisy/tmp-php.php');






















// ##################################################
// get('/', 'page/home.php');
get('/static/$filename', 'page/static.php'); // set static folder
any('/404','page/404.php');

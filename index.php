<?php

require('composants/_config.php'); 
Autoload::start();

$page = isset($_GET['path']) ? $_GET['path'] : '/';

$route = new Router();
$route->get($page);

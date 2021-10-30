<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Src\System\DatabaseConnector;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$db_connection = (new DatabaseConnector())->getConnection();


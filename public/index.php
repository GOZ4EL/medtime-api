<?php
require "../bootstrap.php";
use Src\Controller\DoctorController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if ($uri[1] !== 'doctor') {
  header("HTTP/1.1 404 Not Found");
  exit();
}

$doctor_ci = null;
if (isset($uri[2])) {
  $doctor_ci = $uri[2];
}

$request_method = $_SERVER["REQUEST_METHOD"];

$controller = new DoctorController($db_connection, $request_method, $doctor_ci);
$controller->processRequest();

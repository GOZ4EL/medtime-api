<?php
require "../bootstrap.php";
use Src\Controller\DoctorController;
use Src\Controller\PatientController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$endpoint = strtolower($uri[1]);
$request_method = $_SERVER["REQUEST_METHOD"];

switch ($endpoint) {
  case 'doctor':
    $doctor_ci = null;
    if (isset($uri[2])) {
      $doctor_ci = $uri[2];
    }
    $controller = new DoctorController($db_connection, $request_method, $doctor_ci);
   break;
  case 'patient':
    $patient_ci = null;
    if (isset($uri[2])) {
      $patient_ci = $uri[2];
    }
    $controller = new PatientController($db_connection, $request_method, $patient_ci);
    break;
  default:
    header("HTTP/1.1 404 Not Found");
    echo json_encode(array(
      'error' => 'Invalid path'
    ));
    exit();
}

$controller->processRequest();

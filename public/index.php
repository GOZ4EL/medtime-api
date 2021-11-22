<?php
require "../bootstrap.php";
use Src\Utils\Utils;
use Src\Controller\UserController;
use Src\Controller\DoctorController;
use Src\Controller\PatientController;
use Src\Controller\AppointmentController;

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
  case 'user':
    $login = false;
    if (isset($uri[2]) && strtolower($uri[2]) === "login") {
      $login = true;
    }
    $controller = new UserController($db_connection, $request_method, $login);
    break;
  case 'appointment':
    $id = null;
    $doctor_ci = null;
    $patient_ci = null;

    if (isset($uri[2]) && ! is_numeric($uri[2]) && $uri[2] != '' &&
        (($uri[2] != 'doctor' && $uri[2] != 'patient') ||
         (! is_numeric($uri[3])))) {
      $response = Utils::notFoundResponse(); 
      header($response['status_code_header']);
      echo($response['body']);
      return;
    }

    if (isset($uri[2]) && is_numeric($uri[2])) {
      $id = $uri[2];
    } else if (isset($uri[2]) && isset($uri[3]) &&
               strtolower($uri[2]) === 'doctor') {
      $doctor_ci = $uri[3];
    } else if (isset($uri[2]) && isset($uri[3]) &&
               strtolower($uri[2]) === 'patient') {
      $patient_ci = $uri[3];
    }
    $controller = new AppointmentController($db_connection, $request_method, $id,
                                            $doctor_ci, $patient_ci);
    break;
  default:
    header("HTTP/1.1 404 Not Found");
    echo json_encode(array(
      'error' => 'Invalid path'
    ));
    exit();
}
$controller->processRequest();

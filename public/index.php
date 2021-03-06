<?php
require "../bootstrap.php";
use Src\Utils\Utils;
use Src\Controller\UserController;
use Src\Controller\DoctorController;
use Src\Controller\PatientController;
use Src\Controller\SpecialityController;
use Src\Controller\SpecializationController;
use Src\Controller\AppointmentController;
use Src\Controller\AddressController;
use Src\Controller\AdminController;

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
  case 'speciality':
    $name = isset($uri[2]) ? $uri[2] : null;
    $controller = new SpecialityController($db_connection, $request_method, $name);
    break;
  case 'specialization':
    if (!isset($uri[2]) && $uri[2] != '' &&
        (($uri[2] != 'doctor' && $uri[2] != 'speciality') ||
         ! isset($uri[3]))) {
      $response = Utils::notFoundResponse();
      header($response['status_code_header']);
      echo($response['body']);
      return;
    }

    $doctor_ci = false;
    $speciality_name = false;
    $specialization_id = false;

    if (isset($uri[2]) && is_numeric($uri[2])){
      $specialization_id = $uri[2];
    }else if (isset($uri[2]) && strtolower($uri[2]) === 'doctor') {
      $doctor_ci = $uri[3];
    } else if (isset($uri[2]) && strtolower($uri[2]) === 'speciality') {
      $speciality_name = $uri[3];
    }
    $controller = new SpecializationController($db_connection, $request_method, 
                                               $doctor_ci, $speciality_name,$specialization_id);
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
  case 'address':
    $city_id = isset($uri[2]) ? $uri[2] : null;
    $controller = new AddressController($db_connection, $request_method, $city_id);
    break;
  case 'admin':
    if (!isset($uri[2]) || $uri[2] != 'reports') {
      header("HTTP/1.1 404 Not Found");
      echo json_encode(array(
        'error' => 'Invalid path'
      ));
      exit();
    }
    $controller = new AdminController($db_connection, $request_method);
    break;
  default:
    header("HTTP/1.1 404 Not Found");
    echo json_encode(array(
      'error' => 'Invalid path'
    ));
    exit();
}
$controller->processRequest();

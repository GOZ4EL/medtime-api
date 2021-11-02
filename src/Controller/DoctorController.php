<?php
namespace Src\Controller;

use Src\Utils\Utils;
use Src\TableGateways\UserGateway;
use Src\TableGateways\DoctorGateWay;

class DoctorController {
  
  private $db;
  private $request_method;
  private $doctor_ci;
  
  private $user_gateway;
  private $doctor_gateway;

  public function __construct(\PDO $db, $request_method, $doctor_ci)
  {
    $this->db = $db;
    $this->request_method = $request_method;
    $this->doctor_ci = $doctor_ci;

    $this->user_gateway = new UserGateway($db);
    $this->doctor_gateway = new DoctorGateWay($db);
  }

  public function processRequest(): Void
  {
    switch ($this->request_method) {
      case 'GET':
        if ($this->doctor_ci) {
          $response = $this->getDoctor($this->doctor_ci);
        } else {
          $response = $this->getAllDoctors();
        };
        break;
      case 'POST':
        $response = $this->CreateDoctorFromRequest();
        break;
      case 'PUT':
        $response = $this->updateDoctorFromRequest();
        break;
      case 'DELETE':
        $response = $this->deleteDoctor($this->doctor_ci);
        break;
      default:
        $response = Utils::notFoundResponse();
        break;
    }
    header($response['status_code_header']);
    if ($response['body']) {
      echo $response['body'];
    }
  }

  private function getAllDoctors(): Array
  {
    $result = $this->doctor_gateway->findAll();
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function getDoctor($ci): Array
  {
    $result = $this->doctor_gateway->find($ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function createDoctorFromRequest()
  {
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (! $this->validateDoctor($input)) {
      return Utils::unprocessableEntityResponse();
    }
    $user_id = $this->createUser($input);

    $doctor_input = array(
      'ci' => $input['ci'],
      'firstname' => $input['firstname'],
      'lastname' => $input['lastname'],
      'starts_at' => $input['starts_at'],
      'ends_at' => $input['ends_at'],
      'cost' => $input['cos'],
    );

    $this->doctor_gateway->insert($doctor_input, $user_id);
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = null;
    return $response;
  }

  private function createUser(Array $input): Int
  {
    $user_input = array(
      'email' => $input['email'],
      'role' => $input['role'],
      'password' => $input['password'],
    );

    return $this->user_gateway->insert($user_input);
  }

  private function validateDoctor(Array $input): Bool
  {
    if (! isset($input['email']) || 
        ! filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
      return false;
    }
    if (! isset($input['password']) || 
        strlen($input['password']) < 8) {
      return false;
    }
    if (! isset($input['role']) || 
        strtolower($input['role']) !== 'doctor') {
      return false;
    }
    if (! isset($input['ci']) ||
        strlen($input['ci']) < 5 ||
        ! empty($this->doctor_gateway->find($input['ci']))) {
      return false;
    }
    if (! isset($input['firstname'])) {
      return false;
    }
    if (! isset($input['lastname'])) {
      return false;
    }
    if (! isset($input['starts_at'])) {
      return false;
    }
    if (! isset($input['ends_at'])) {
      return false;
    }
    if (! isset($input['cost'])) {
      return false;
    }

    return true;
  }
}


<?php
namespace Src\Controller;

use Src\Utils\Utils;
use Src\TableGateways\DoctorGateway;
use Src\Controller\UserController;

class DoctorController {
  
  private $db;
  private $request_method;
  private $doctor_ci;
  
  private $user_controller;
  private $doctor_gateway;

  public function __construct(\PDO $db, $request_method, $doctor_ci)
  {
    $this->db = $db;
    $this->request_method = $request_method;
    $this->doctor_ci = $doctor_ci;

    $this->doctor_gateway = new DoctorGateway($db);
    $this->user_controller = new UserController($db);
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
        $response = $this->updateDoctorFromRequest($this->doctor_ci);
        break;
      case 'DELETE':
        $response = $this->deleteDoctor($this->doctor_ci);
        break;
      default:
        $response = Utils::notFoundResponse();
        break;
    }
    header($response['status_code_header']);
    echo $response['body'];
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

    $user_id = $this->user_controller->createUser($input);

    if (gettype($user_id) === "array") {
      return $user_id;
    }

    $doctor_input = array(
      'ci' => $input['ci'],
      'firstname' => $input['firstname'],
      'lastname' => $input['lastname'],
      'starts_at' => $input['starts_at'],
      'ends_at' => $input['ends_at'],
      'cost' => $input['cost'],
    );

    $this->doctor_gateway->insert($doctor_input, $user_id);
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = json_encode([
      'message' => 'Doctor registered successfully'
    ]);
    return $response;
  }

  private function updateDoctorFromRequest($ci)
  {
    $result = $this->doctor_gateway->find($ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (! $this->validateDoctor($input)) {
      return Utils::unprocessableEntityResponse();
    }
    $this->doctor_gateway->update($ci, $input);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode([
      'message' => 'Doctor updated successfully'
    ]);
    return $response;
  } 

  private function deleteDoctor($ci) 
  {
    $result = $this->doctor_gateway->find($ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $this->doctor_gateway->delete($ci);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode([
      'message' => 'Doctor deleted successfully'
    ]);
    return $response;
  }

  private function validateDoctor(Array $input): Bool
  {
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


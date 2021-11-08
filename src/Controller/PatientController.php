<?php
namespace Src\Controller;

use Src\Utils\Utils;
use Src\TableGateways\PatientGateway;
use Src\Controller\UserController;

class PatientController {

  private $db;
  private $request_method;
  private $patient_ci;

  private $user_controller;
  private $patient_gateway;

  public function __construct(\PDO $db, $request_method, $patient_ci)
  {
    $this->db = $db;
    $this->request_method = $request_method;
    $this->patient_ci = $patient_ci;

    $this->patient_gateway = new PatientGateway($db);
    $this->user_controller = new UserController($db);
  }

  public function processRequest(): Void
  {
    switch ($this->request_method) {
      case 'GET':
        if ($this->patient_ci) {
          $response = $this->getPatient($this->patient_ci);
        } else {
          $response = $this->getAllPatients();
        };
        break;
      case 'POST':
        $response = $this->CreatePatientFromRequest();
        break;
      case 'PUT':
        $response = $this->updatePatientFromRequest($this->patient_ci);
        break;
      case 'DELETE':
        $response = $this->deletePatient($this->patient_ci);
        break;
      default:
        $response = Utils::notFoundResponse();
        break;
    }
    header($response['status_code_header']);
    echo $response['body'];
  }

  private function getAllPatients(): Array
  {
    $result = $this->patient_gateway->findAll();
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function getPatient($ci): Array
  {
    $result = $this->patient_gateway->find($ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function createPatientFromRequest()
  {
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (! $this->validatePatient($input) ||
        ! empty($this->patient_gateway->find($input['ci']))) {
      return Utils::unprocessableEntityResponse();
    }

    $user_id = $this->user_controller->createUser($input);

    if (gettype($user_id) === "array") {
      return $user_id;
    }

    $patient_input = array(
      'ci' => $input['ci'],
      'firstname' => $input['firstname'],
      'lastname' => $input['lastname'],
      'starts_at' => $input['starts_at'],
      'ends_at' => $input['ends_at'],
      'cost' => $input['cost'],
    );

    $this->patient_gateway->insert($patient_input, $user_id);
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = json_encode([
      'message' => 'Patient registered successfully'
    ]);
    return $response;
  }

  private function updatePatientFromRequest($ci)
  {
    $result = $this->patient_gateway->find($ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (! $this->validatePatient($input)) {
      return Utils::unprocessableEntityResponse();
    }
    $this->patient_gateway->update($ci, $input);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode([
      'message' => 'Patient updated successfully'
    ]);
    return $response;
  } 

  private function deletePatient($ci) 
  {
    $result = $this->patient_gateway->find($ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $this->patient_gateway->delete($ci);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode([
      'message' => 'Patient deleted successfully'
    ]);
    return $response;
  }

  private function validatePatient(Array $input): Bool
  {
    if (! isset($input['ci']) ||
        strlen($input['ci']) < 5) {
      return false;
    }
    if (! isset($input['firstname'])) {
      return false;
    }
    if (! isset($input['lastname'])) {
      return false;
    }
    if (! isset($input['city_id'])) {
      return false;
    }

    return true;
  }
}


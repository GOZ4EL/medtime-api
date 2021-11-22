<?php
namespace Src\Controller;

use Src\Utils\Utils;
use Src\TableGateways\SpecializationGateway;
use Src\Controller\UserController;

class SpecializationController {

  private $db;
  private $request_method;
  private $specialization_id;

  private $user_controller;
  private $specialization_gateway;

  public function __construct(\PDO $db, $request_method, $specialization_id)
  {
    $this->db = $db;
    $this->request_method = $request_method;
    $this->specialization_id = $specialization_id;

    $this->specialization_gateway = new SpecializationGateway($db);
    $this->user_controller = new UserController($db);
  }

  public function processRequest(): Void
  {
    switch ($this->request_method) {
      case 'GET':
        if ($this->specialization_id) {
          $response = $this->getSpecialization($this->specialization_id);
        } else {
          $response = $this->getAllSpecializations();
        };
        break;
      case 'POST':
        $response = $this->CreateSpecializationFromRequest();
        break;
      case 'DELETE':
        $response = $this->deleteSpecialization($this->specialization_id);
        break;
      case 'OPTIONS':
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
          'message' => 'Access Granted'
        ]);
        break;
      default:
        $response = Utils::notFoundResponse();
        break;
    }
    header($response['status_code_header']);
    echo $response['body'];
  }

  private function getAllSpecializations(): Array
  {
    $result = $this->specialization_gateway->findAll();
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function getSpecialization($doctor_ci): Array
  {
    $result = $this->specialization_gateway->find($doctor_ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function createSpecializationFromRequest()
  {
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (! $this->validateSpecialization($input)) {
      return Utils::unprocessableEntityResponse();
    }

    $specialization_input = array(
      'doctor_ci' => $input['doctor_ci'],
      'speciality_name' => $input['speciality_name'],
    );

    $this->specialization_gateway->insert($specialization_input);
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = json_encode([
      'message' => 'Specialization registered successfully'
    ]);
    return $response;
  }

  private function deleteSpecialization($ci) 
  {
    $result = $this->specialization_gateway->find($ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $this->specialization_gateway->delete($ci);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode([
      'message' => 'Specialization deleted successfully'
    ]);
    return $response;
  }

  private function validateSpecialization(Array $input): Bool
  {
    if (! isset($input['doctor_ci']) ||
        strlen($input['doctor_ci']) < 5) {
      return false;
    }
    if (! isset($input['speciality_name'])) {
      return false;
    }
    return true;
  }
}


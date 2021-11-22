<?php
namespace Src\Controller;

use Src\Utils\Utils;
use Src\TableGateways\SpecialityGateway;

class SpecialityController {

  private $db;
  private $request_method;
  private $speciality_name;
  private $speciality_gateway;

  public function __construct(\PDO $db, $request_method, $speciality_name)
  {
    $this->db = $db;
    $this->request_method = $request_method;
    $this->speciality_name = $speciality_name;

    $this->speciality_gateway = new SpecialityGateway($db);
  }

  public function processRequest(): Void
  {
    switch ($this->request_method) {
      case 'GET':
        $response = $this->getAllSpecialities();
        break;
      case 'POST':
        $response = $this->CreateSpecialityFromRequest();
        break;
      case 'DELETE':
        $response = $this->deleteSpeciality($this->speciality_ci);
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

  private function getAllSpecialities(): Array
  {
    $result = $this->speciality_gateway->findAll();
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function createSpecialityFromRequest()
  {
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (! $this->validateSpeciality($input) ||
        ! empty($this->speciality_gateway->find($input['name']))) {
      return Utils::unprocessableEntityResponse();
    }

    $speciality_input = array(
      'name' => $input['name'],
    );

    $this->speciality_gateway->insert($speciality_input);
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = json_encode([
      'message' => 'Speciality created successfully'
    ]);
    return $response;
  }

  private function deleteSpeciality($name) 
  {
    $result = $this->speciality_gateway->find($name);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $this->speciality_gateway->delete($name);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode([
      'message' => 'Speciality deleted successfully'
    ]);
    return $response;
  }

  private function validateSpeciality(Array $input): Bool
  {
    if (! isset($input['name'])) {
      return false;
    }
    return true;
  }
}


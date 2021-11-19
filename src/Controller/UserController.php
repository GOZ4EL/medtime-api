<?php
namespace Src\Controller;

use Src\TableGateways\UserGateway;
use Src\Utils\Utils;

class UserController {
  
  private $db;
  private $request_method;
  private $login;
  private $user_gateway;

  public function __construct(\PDO $db, $request_method = null, $login = null) {
    $this->db = $db;
    $this->request_method = $request_method;
    $this->login = $login;
    $this->user_gateway = new UserGateway($db);
  }

  public function processRequest()
  {
    switch ($this->request_method) {
      case 'POST':
        if ($this->login) {
          $response = $this->logUserIn();
        } else {
          $response = Utils::notFoundResponse();
        }
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

  private function logUserIn()
  {
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (! $this->validateUser($input)) {
      return Utils::notFoundResponse();
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($this->user_gateway->login($input), true);
    return $response;
  }

  public function createUser(Array $input): Int|Array
  {
    if (! $this->validateUser($input)) {
      return Utils::unprocessableEntityResponse();
    }
    $user_input = array(
      'email' => $input['email'],
      'role' => $input['role'],
      'password' => $input['password'],
    );

    return $this->user_gateway->insert($user_input);
  }

  private function validateUser(Array $input): Bool
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
        (strtolower($input['role']) !== 'doctor' &&
         strtolower($input['role'] !== 'patient'))) {
      return false;
    }

    return true;
  }

}


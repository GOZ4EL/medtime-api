<?php
namespace Src\Controller;

use Src\TableGateways\UserGateway;
use Src\Utils\Utils;

class UserController {
  
  private $db;
  private $user_gateway;

  public function __construct(\PDO $db) {
    $this->db = $db;
    $this->user_gateway = new UserGateway($db);
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
        strtolower($input['role']) !== 'doctor') {
      return false;
    }

    return true;
  }

}


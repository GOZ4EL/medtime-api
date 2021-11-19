<?php
namespace Src\TableGateways;

use DateTimeImmutable;
use Firebase\JWT\JWT;

class UserGateway {

  private $db = null;

  public function __construct(\PDO $db)
  {
    $this->db = $db;
  }

  public function find($id): Int
  {
    $statement = "
      SELECT *
      FROM User
      WHERE id = ?;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($id));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function insert(Array $input): Int
  {
    $statement = "
      INSERT INTO User 
        (id, email, role, password)
      VALUES
        (NULL, :email, :role, :password);
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        "email" => $input["email"],
        "role" => $input["role"],
        "password" => password_hash($input["password"], PASSWORD_BCRYPT),
      ));
      $user_id = $this->db->lastInsertId();
      return $user_id;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function login(Array $input)
  {
    $statement = "
      SELECT *
      FROM User
      WHERE email = ?
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($input['email']));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC)[0];
      
      if ($result == NULL) {
        $result = array(
          'message' => 'Invalid email',
        );
        return $result;
      } 

      $hashed_pass = $result['password'];
      if (password_verify($input['password'], $hashed_pass)) {
        $jwt = $this->GenerateJWT($result['role']);

        $result = array(
          'id' => $result['id'],
          'email' => $result['email'],
          'role' => $result['role'],
          'jwt' => $jwt
        );
      } else {
        $result = array(
          'message' => 'Invalid password',
        );
      }
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  private function GenerateJWT($role) 
  {
    $secret_key  = getenv("JWT_SECRET");
    $issued_at   = new DateTimeImmutable();
    $expire      = $issued_at->modify('1 week')->getTimestamp();
    $server_name = "medtime.com.ve";

    $data = array(
      'iat' => $issued_at->getTimestamp(),
      'iss' => $server_name,
      'nbf' => $issued_at->getTimestamp(),
      'exp' => $expire,
      'role' => $role,
    );

    return JWT::encode(
      $data,
      $secret_key,
      'HS512'
    );
  }
}


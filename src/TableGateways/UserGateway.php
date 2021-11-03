<?php
namespace Src\TableGateways;

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
}


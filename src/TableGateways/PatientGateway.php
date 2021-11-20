<?php
namespace Src\TableGateways;

class PatientGateway {

  private $db = null;

  public function __construct(\PDO $db)
  {
    $this->db = $db;
  }

  public function findAll(): Array
  {
    $statement = "
      SELECT *
      FROM Patient
        ORDER BY user_id;
    ";

    try {
      $statement = $this->db->query($statement);
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function find($ci): Array
  {
    $statement = "
      SELECT *
      FROM Patient
      WHERE ci = ?;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($ci));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function insert(Array $input, $user_id): Int
  {
    $statement = "
      INSERT INTO Patient
        (ci, user_id, city_id, firstname, lastname)
      VALUES
        (:ci, :user_id, :city_id, :firstname, :lastname);
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'ci' => $input['ci'],
        'user_id' => $user_id,
        'city_id' => $input['city_id'],
        'firstname' => $input['firstname'],
        'lastname' => $input['lastname'],
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function update($ci, Array $input): Int
  {
    $statement = "
      UPDATE Patient
      SET
        city_id = :city_id,
        firstname = :firstname,
        lastname = :lastname
      WHERE ci = :ci;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'ci' => (int) $ci,
        'city_id' => $input['city_id'],
        'firstname' => $input['firstname'],
        'lastname' => $input['lastname'],
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function delete($ci): Int
  {
    $statement = "
      DELETE u 
      FROM Patient p
        JOIN User u
          ON p.user_id = u.id
      WHERE p.ci = :ci;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array('ci' => $ci));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function findByUserId($user_id)
  {
    $statement = "
      SELECT *
      FROM Patient
      WHERE user_id = ?;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($user_id));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC)[0];
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
 
  }
}


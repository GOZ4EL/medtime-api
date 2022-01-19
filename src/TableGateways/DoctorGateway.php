<?php
namespace Src\TableGateways;

class DoctorGateWay {

  private $db = null;

  public function __construct(\PDO $db)
  {
    $this->db = $db;
  }

  public function findAll(): Array
  {
    $statement = "
      SELECT *
      FROM Doctor
        ORDER BY firstname asc;
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
      FROM Doctor
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
      INSERT INTO Doctor
        (ci, user_id, firstname, lastname, starts_at, ends_at, cost, image)
      VALUES
        (:ci, :user_id, :firstname, :lastname, :starts_at, :ends_at, :cost, :image);
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'ci' => $input['ci'],
        'user_id' => $user_id,
        'firstname' => $input['firstname'],
        'lastname' => $input['lastname'],
        'starts_at' => $input['starts_at'],
        'ends_at' => $input['ends_at'],
        'cost' => $input['cost'],
        'image' => $input['image'],
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function update($ci, Array $input): Int
  {
    $statement = "
      UPDATE Doctor
      SET
        firstname = :firstname,
        lastname = :lastname,
        starts_at = :starts_at,
        ends_at = :ends_at,
        cost = :cost,
        image = :image
      WHERE ci = :ci;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'ci' => (int) $ci,
        'firstname' => $input['firstname'],
        'lastname' => $input['lastname'],
        'starts_at' => $input['starts_at'],
        'ends_at' => $input['ends_at'],
        'cost' => $input['cost'],
        'image' => $input['image'],
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
      FROM Doctor d
        JOIN User u
          ON d.user_id = u.id
      WHERE d.ci = :ci;
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
      FROM Doctor
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


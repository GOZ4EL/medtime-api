<?php
namespace Src\TableGateways;

class SpecialityGateWay {

  private $db = null;

  public function __construct(\PDO $db)
  {
    $this->db = $db;
  }

  public function findAll(): Array
  {
    $statement = "
      SELECT *
      FROM Speciality;
    ";

    try {
      $statement = $this->db->query($statement);
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function find($name): Array
  {
    $statement = "
      SELECT *
      FROM Speciality
      WHERE name = ?;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($name));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function insert(Array $input): Int
  {
    $statement = "
      INSERT INTO Speciality
      VALUES
        (:name);
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'name' => $input['name'],
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function delete($name): Int
  {
    $statement = "
      DELETE
      FROM Speciality
      WHERE name = :name;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array('name' => $name));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }
}


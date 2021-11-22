<?php
namespace Src\TableGateways;

class SpecializationGateWay {

  private $db = null;

  public function __construct(\PDO $db)
  {
    $this->db = $db;
  }

  public function findAll(): Array
  {
    $statement = "
      SELECT *
      FROM Specialization
        ORDER BY id;
    ";

    try {
      $statement = $this->db->query($statement);
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function find($doctor_ci): Array
  {
    $statement = "
      SELECT *
      FROM Specialization
      WHERE doctor_ci = ?;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($doctor_ci));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function insert(Array $input): Int
  {
    $statement = "
      INSERT INTO Specialization
        (doctor_ci, speciality_name)
      VALUES
        (:doctor_ci, :speciality_name);
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'doctor_ci' => $input['doctor_ci'],
        'speciality_name' => $input['speciality_name'],
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function delete($id): Int
  {
    $statement = "
      DELETE
      FROM Specialization
      WHERE id = :id;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array('id' => $id));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }
}


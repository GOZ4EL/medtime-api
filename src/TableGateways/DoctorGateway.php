<?php
namespace Src\TableGateways;

class DoctorGateWay {

  private $db = null;

  public function __construct(\PDO $db)
  {
    $this->db = $db;
  }

  public function findAll(): array
  {
    $statement = "SELECT * FROM Doctor;";

    try {
      $statement = $this->db->query($statement);
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function find($id): array
  {
    $statement = "
      SELECT d.*
      FROM Doctor d
        JOIN User u
          ON d.user_id=u.id
      WHERE u.id = ?;
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
}


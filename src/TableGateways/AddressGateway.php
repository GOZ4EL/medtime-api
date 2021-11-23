<?php
namespace Src\TableGateways;

class AddressGateWay {

  private $db = null;

  public function __construct(\PDO $db)
  {
    $this->db = $db;
  }

  public function findAll(): Array
  {
    $statement = "
      SELECT 
        c.id AS `city_id`, 
        c.name AS `city_name`,
        s.id AS `state_id`,
        s.name AS `state_name`
      FROM City c
        JOIN State s
          ON s.id = c.state_id
        ORDER BY c.id;
    ";

    try {
      $statement = $this->db->query($statement);
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function find($city_id): Array
  {
    $statement = "
      SELECT 
        c.name AS `city_name`, 
        s.name AS `state_name`
      FROM City c
        JOIN State s
          ON s.id = c.state_id
      WHERE c.id = ?;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($city_id));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }
}
 

<?php
namespace Src\TableGateways;

class UserGateway {

  private $db = null;

  public function __construct(\PDO $db)
  {
    $this->db = $db;
  }
}


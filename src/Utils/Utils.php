<?php
namespace Src\Utils;

use \Exception;

class Utils {
  public static function unprocessableEntityResponse()
  {
    $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
    $response['body'] = json_encode([
      'error' => 'Invalid input'
    ]);
    return $response;
  }

  public static function notFoundResponse()
  {
    $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
    $response['body'] = json_encode([
      'error' => 'Object not found'
    ]);
    return $response;
  }

  public static function emptyTestDatabase(): Void
  {
    require dirname(__FILE__) . '/../../bootstrap.php';

    if (getenv('DB_DATABASE') != 'testing_medtime') {
      throw new Exception("The current database isn't the test one");
    }
    $queries = array(
      "DELETE FROM User",
      "ALTER TABLE User AUTO_INCREMENT = 1",
      "ALTER TABLE Admin AUTO_INCREMENT = 1",
      "ALTER TABLE Specialization AUTO_INCREMENT = 1",
      "ALTER TABLE Appointment AUTO_INCREMENT = 1",
    );
    foreach($queries as $query) {
      $db_connection->exec($query);
    }
  }
}


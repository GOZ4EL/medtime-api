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
    $response['body'] = null;
    return $response;
  }

  public static function makeTestDatabaseEmpty(): Void
  {
    require dirname(__FILE__) . '/../../bootstrap.php';

    if (getenv('DB_DATABASE') != 'testing_medtime') {
      throw new Exception("The current database isn't the test one");
    }
    $sql = "DELETE FROM User";
    $db_connection->exec($sql);
  }
}


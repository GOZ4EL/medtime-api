<?php
namespace Src\Controller;

use Src\Utils\Utils;
use Src\Reports\Reports;

class AdminController {
  
  private $db;
  private $request_method;

  public function __construct(\PDO $db, $request_method = null) {
    $this->db = $db;
    $this->request_method = $request_method;
    $this->reports = new Reports($db);
  }

  public function processRequest()
  {
    switch ($this->request_method) {
      case 'GET':
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $this->reports->showReport();
        break;
      case 'OPTIONS':
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
          'message' => 'Access Granted'
        ]);
        break;
      default:
        $response = Utils::notFoundResponse();
        break;
    }
    header($response['status_code_header']);
    echo $response['body'];
  }
}

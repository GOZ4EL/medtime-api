<?php
namespace Src\Controller;

use Src\Utils\Utils;
use Src\TableGateways\AddressGateway;

class AddressController {

  private $db;
  private $request_method;
  private $address_id;

  private $address_gateway;

  public function __construct(\PDO $db, $request_method, $city_id)
  {
    $this->db = $db;
    $this->request_method = $request_method;
    $this->city_id = $city_id;

    $this->address_gateway = new AddressGateway($db);
  }

  public function processRequest(): Void
  {
    switch ($this->request_method) {
      case 'GET':
        if ($this->city_id) {
          $response = $this->getAddress($this->city_id);
        } else {
          $response = $this->getAllAddresses();
        };
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

  private function getAllAddresses(): Array
  {
    $result = $this->address_gateway->findAll();
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function getAddress($city_id): Array
  {
    $result = $this->address_gateway->find($city_id);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

} 

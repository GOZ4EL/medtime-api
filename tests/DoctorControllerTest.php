<?php
include(dirname(__FILE__) . "/../src/Utils/Utils.php");
use GuzzleHttp\Client;
use Src\Utils\Utils;

class DoctorControllerTest extends \PHPUnit\Framework\TestCase
{

  public function createDummyDoctor(Array $data = null)
  {
    $dummy_doctor = array(
      'email' => 'test1@test.com',
      'role' => 'doctor',
      'password' => 'TestPass123',
      'ci' => '12345678',
      'firstname' => 'Testing',
      'lastname' => 'Just One',
      'starts_at' => '10:00',
      'ends_at' => '16:00',
      'cost' => '50'
    );

    if(isset($data)) {
      foreach($dummy_doctor as $key => $value) {
        if (array_key_exists($key, $data)) {
          $dummy_doctor[$key] = $data[$key];
        }
      }
    }
    
    return $dummy_doctor;
  }

  public function test_post()
  {
    Utils::makeTestDatabaseEmpty();

    $client = new Client();
    $data = $this->createDummyDoctor(array(
      'email' => 'hordemzerado@gmail.com',
      'firstname' => 'loko',
      'cost' => '999.99'
    ));
    $response = $client->post('http://127.0.0.1/doctor',['json' => $data]);

    $this->assertEquals(201, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['message' => 'Doctor registered successfully'], 
      $data
    );
  }
}


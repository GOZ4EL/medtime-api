<?php
include(dirname(__FILE__) . "/../src/Utils/Utils.php");
use GuzzleHttp\Client;
use Src\Utils\Utils;

class DoctorControllerTest extends \PHPUnit\Framework\TestCase
{

  private $endpoint = 'http://127.0.0.1/doctor';

  private function createDummyDoctor(Array $data = null): Array
  {
    $dummy_doctor = array(
      'email' => 'test1@test.com',
      'role' => 'doctor',
      'password' => 'TestPass123',
      'ci' => '12345678',
      'firstname' => 'Testing',
      'lastname' => 'Just One',
      'starts_at' => '10:00:00',
      'ends_at' => '16:00:00',
      'cost' => '50.00'
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

  public function testPostWithValidData()
  {
    Utils::emptyTestDatabase();

    $client = new Client();
    $data = $this->createDummyDoctor();
    $response = $client->post($this->endpoint, ['json' => $data]);

    $this->assertEquals(201, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['message' => 'Doctor registered successfully'], 
      $data
    );

    Utils::emptyTestDatabase();
  }

  public function testPostWithInvalidData()
  {
    $this->expectException(GuzzleHttp\Exception\ClientException::class);

    $client = new Client();
    $data = $this->createDummyDoctor(['email' => 'invalid email']);
    $response = $client->post($this->endpoint, ['json' => $data]);

    $this->assertEquals(422, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['error' => 'Invalid input'],
      $data
    );
  }
}


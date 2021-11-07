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

  public function testGetWithoutArguments()
  {
    $client = new Client();
    $doctor1 = $this->createDummyDoctor();
    $doctor2 = $this->createDummyDoctor([
      'email' => 'example@example.com',
      'ci' => '23456756',
    ]);
    $client->post($this->endpoint, ['json' => $doctor1]);
    $client->post($this->endpoint, ['json' => $doctor2]);
    $response = $client->get($this->endpoint);

    $doctor1 = array(
      'ci' => $doctor1['ci'],
      'user_id' => 1,
      'firstname' => $doctor1['firstname'],
      'lastname' => $doctor1['lastname'],
      'starts_at' => $doctor1['starts_at'],
      'ends_at' => $doctor1['ends_at'],
      'cost' => $doctor1['cost']
    );

    $doctor2 = array(
      'ci' => $doctor2['ci'],
      'user_id' => 2,
      'firstname' => $doctor2['firstname'],
      'lastname' => $doctor2['lastname'],
      'starts_at' => $doctor2['starts_at'],
      'ends_at' => $doctor2['ends_at'],
      'cost' => $doctor2['cost']
    );

    $this->assertEquals(200, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals([$doctor1, $doctor2], $data);

    Utils::emptyTestDatabase();
  }

  public function testGetWithExpectedArguments()
  {
    $client = new Client();
    $doctor = $this->createDummyDoctor();
    $client->post($this->endpoint, ['json' => $doctor]);

    $doctor = array(
      'ci' => $doctor['ci'],
      'user_id' => 1,
      'firstname' => $doctor['firstname'],
      'lastname' => $doctor['lastname'],
      'starts_at' => $doctor['starts_at'],
      'ends_at' => $doctor['ends_at'],
      'cost' => $doctor['cost']
    );
    $ci = $doctor['ci'];

    $response = $client->get($this->endpoint . "/$ci");

    $this->assertEquals(200, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals([$doctor], $data);

    Utils::emptyTestDatabase();
  }

  public function testGetWithInvalidArguments()
  {
    $this->expectException(GuzzleHttp\Exception\ClientException::class);

    $client = new Client();
    $doctor = $this->createDummyDoctor();
    $client->post($this->endpoint, ['json' => $doctor]);
    $response = $client->get($this->endpoint . "/1");

    $this->assertEquals(404, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['error' => 'Object not found'],
      $data
    );
    Utils::emptyTestDatabase();
  }
}


<?php
include(dirname(__FILE__) . "/../src/Utils/Utils.php");
use GuzzleHttp\Client;
use Src\Utils\Utils;

class UserControllerTest extends \PHPUnit\Framework\TestCase
{
  private $endpoint = 'http://127.0.0.1/';

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

  private function createDummyPatient(Array $data = null): Array
  {
    $dummy_patient = array(
      'email' => 'test1@test.com',
      'role' => 'patient',
      'password' => 'TestPass123',
      'ci' => '12345678',
      'firstname' => 'Testing',
      'lastname' => 'Just One',
      'city_id' => '1',
    );

    if (isset($data)) {
      foreach ($dummy_patient as $key => $value) {
        if (array_key_exists($key, $data)) {
          $dummy_patient[$key] = $data[$key];
        }
      }
    }
    
    return $dummy_patient;
  }

  public function testLoginWithValidCredentials()
  {
    Utils::emptyTestDatabase();

    $client = new Client();
    $doctor = $this->createDummyDoctor();
    $client->post($this->endpoint . 'doctor', ['json' => $doctor]);

    $response = $client->post(
      $this->endpoint . 'user/login',
      ['json' => array(
        'email' => $doctor['email'],
        'role' => $doctor['role'],
        'password' => $doctor['password'],
      )]
    );
    $data = json_decode($response->getBody(true), true);
    var_dump($data);
    $this->assertArrayHasKey('jwt', $data);
  }

  public function testLoginWithInvalidEmail()
  {
    Utils::emptyTestDatabase();

    $client = new Client();
    $doctor = $this->createDummyDoctor();
    $client->post($this->endpoint . 'doctor', ['json' => $doctor]);

    $response = $client->post(
      $this->endpoint . 'user/login',
      ['json' => array(
        'email' => 'testinvalid@test.com',
        'role' => $doctor['role'],
        'password' => $doctor['password'],
      )]
    );
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['message' => 'Invalid email'],
      $data
    );
  }
  
  public function testLoginWithInvalidPassword()
  {
    Utils::emptyTestDatabase();

    $client = new Client();
    $doctor = $this->createDummyDoctor();
    $client->post($this->endpoint . 'doctor', ['json' => $doctor]);

    $response = $client->post(
      $this->endpoint . 'user/login',
      ['json' => array(
        'email' => $doctor['email'],
        'role' => $doctor['role'],
        'password' => 'Invalid Password',
      )]
    );
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['message' => 'Invalid password'],
      $data
    );
 
  }
}


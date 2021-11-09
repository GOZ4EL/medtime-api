<?php
include(dirname(__FILE__) . "/../src/Utils/Utils.php");
use Src\Utils\Utils;
use GuzzleHttp\Client;

class PatientControllerTest extends \PHPUnit\Framework\TestCase
{

  private $endpoint = 'http://127.0.0.1/patient';

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

  public function testPostWithValidData()
  {
    Utils::emptyTestDatabase();

    $client = new Client();
    $data = $this->createDummyPatient();
    $response = $client->post($this->endpoint, ['json' => $data]);

    $this->assertEquals(201, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['message' => 'Patient registered successfully'], 
      $data
    );
  }

  public function testPostWithInvalidData()
  {
    $this->expectException(GuzzleHttp\Exception\ClientException::class);

    $client = new Client();
    $data = $this->createDummyPatient(['email' => 'invalid email']);
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
    Utils::emptyTestDatabase();

    $client = new Client();
    $patient1 = $this->createDummyPatient();
    $patient2 = $this->createDummyPatient([
      'email' => 'example@example.com',
      'ci' => '23456756',
    ]);
    $client->post($this->endpoint, ['json' => $patient1]);
    $client->post($this->endpoint, ['json' => $patient2]);
    $response = $client->get($this->endpoint);

    $patient1 = array(
      'ci' => $patient1['ci'],
      'user_id' => 1,
      'firstname' => $patient1['firstname'],
      'lastname' => $patient1['lastname'],
      'city_id' => $patient1['city_id'],
    );

    $patient2 = array(
      'ci' => $patient2['ci'],
      'user_id' => 2,
      'firstname' => $patient2['firstname'],
      'lastname' => $patient2['lastname'],
      'city_id' => $patient2['city_id'],
    );

    $this->assertEquals(200, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals([$patient1, $patient2], $data);
  }

  public function testGetWithExpectedArguments()
  {
    Utils::emptyTestDatabase();

    $client = new Client();
    $patient = $this->createDummyPatient();
    $client->post($this->endpoint, ['json' => $patient]);

    $patient = array(
      'ci' => $patient['ci'],
      'user_id' => 1,
      'firstname' => $patient['firstname'],
      'lastname' => $patient['lastname'],
      'city_id' => $patient['city_id'],
    );
    $ci = $patient['ci'];

    $response = $client->get($this->endpoint . "/$ci");

    $this->assertEquals(200, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals([$patient], $data);
  }

  public function testGetWithInvalidArguments()
  {
    Utils::emptyTestDatabase();

    $this->expectException(GuzzleHttp\Exception\ClientException::class);

    $client = new Client();
    $patient = $this->createDummyPatient();
    $client->post($this->endpoint, ['json' => $patient]);
    $response = $client->get($this->endpoint . "/1");

    $this->assertEquals(404, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['error' => 'Object not found'],
      $data
    );
  }

  public function testPutWithValidCiAndValidData()
  {
    Utils::emptyTestDatabase();

    $client = new Client();
    $patient = $this->createDummyPatient();
    $client->post($this->endpoint, ['json' => $patient]);

    $patient['firstname'] = 'Test 2';
    $ci = $patient['ci'];
    $response = $client->put(
      $this->endpoint . "/$ci", 
      ['json' => $patient]
    );

    $this->assertEquals(200, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['message' => 'Patient updated successfully'],
      $data
    );
  }

#  public function testPutWithValidCiAndInvalidData()
#  {
#    Utils::emptyTestDatabase();
#
#    $client = new Client();
#    $patient = $this->createDummyPatient();
#    $client->post($this->endpoint, ['json' => $patient]);
#  }
  
  public function testDelete()
  {
    Utils::emptyTestDatabase();

    $client = new Client();
    $patient = $this->createDummyPatient();
    $client->post($this->endpoint, ['json' => $patient]);

    $ci = $patient['ci'];
    $response = $client->delete($this->endpoint . "/$ci");
    $this->assertEquals(200, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['message' => 'Patient deleted successfully'],
      $data
    );

    $response = $client->get($this->endpoint);
    $data = json_encode($response->getBody(true), true);
    $this->assertEquals('{}', $data);
  }
}


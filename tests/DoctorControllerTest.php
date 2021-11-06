<?php
use GuzzleHttp\Client;

class DoctorControllerTest extends \PHPUnit\Framework\TestCase
{
  public function test_post()
  {
    $client = new Client();

    $data = array(
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

    $response = $client->post('http://127.0.0.1/doctor',['json' => $data]);

    $this->assertEquals(201, $response->getStatusCode());
    $data = json_decode($response->getBody(true), true);
    $this->assertEquals(
      ['message' => 'Doctor registered successfully'], 
      $data
    );
  }
}


<?php
namespace Src\Controller;

use Src\Utils\Utils;
use Src\TableGateways\AppointmentGateway;
use Src\Controller\UserController;

class AppointmentController {

  private $db;
  private $request_method;
  private $id;
  private $doctor_ci;
  private $patient_ci;

  private $user_controller;
  private $appointment_gateway;

  public function __construct(\PDO $db, $request_method, $id,
                              $doctor_ci, $patient_ci)
  {
    $this->db = $db;
    $this->request_method = $request_method;
    $this->id = $id;
    $this->doctor_ci = $doctor_ci;
    $this->patient_ci = $patient_ci;

    $this->appointment_gateway = new AppointmentGateway($db);
    $this->user_controller = new UserController($db);
  }

  public function processRequest(): Void
  {
    switch ($this->request_method) {
      case 'GET':
        if ($this->id) {
          $response = $this->getAppointment($this->id);
        } else if($this->doctor_ci) {
          $response = $this->getAppointmentsByDoctor($this->doctor_ci);
        } else if ($this->patient_ci) {
          $response = $this->getAppointmentsByPatient($this->patient_ci);
        } else {
          $response = $this->getAllAppointments();
        };
        break;
      case 'POST':
        $response = $this->CreateAppointmentFromRequest();
        break;
      case 'PUT':
        $response = $this->updateAppointmentFromRequest($this->id);
        break;
      case 'DELETE':
        $response = $this->deleteAppointment($this->id);
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

  private function getAllAppointments(): Array
  {
    $result = $this->appointment_gateway->findAll();
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function getAppointment($id): Array
  {
    $result = $this->appointment_gateway->find($id);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function getAppointmentsByDoctor($doctor_ci)
  {
    $result = $this->appointment_gateway->findByDoctorCi($doctor_ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function getAppointmentsByPatient($patient_ci)
  {
    $result = $this->appointment_gateway->findByPatientCi($patient_ci);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function createAppointmentFromRequest()
  {
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (! $this->validateAppointment($input)) { 
      return Utils::unprocessableEntityResponse();
    }

    $appointment_input = array(
      'doctor_ci' => $input['doctor_ci'],
      'patient_ci' => $input['patient_ci'],
      'day' => $input['day'],
      'hour' => $input['hour'],
    );

    $this->appointment_gateway->insert($appointment_input);
    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = json_encode([
      'message' => 'Appointment registered successfully'
    ]);
    return $response;
  }

  private function updateAppointmentFromRequest($id)
  {
    $result = $this->appointment_gateway->find($id);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    if (! $this->validateAppointment($input)) {
      return Utils::unprocessableEntityResponse();
    }
    $this->appointment_gateway->update($id, $input);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode([
      'message' => 'Appointment updated successfully'
    ]);
    return $response;
  } 

  private function deleteAppointment($id) 
  {
    $result = $this->appointment_gateway->find($id);
    if (! $result) {
      return Utils::notFoundResponse();
    }
    $this->appointment_gateway->delete($id);
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode([
      'message' => 'Appointment deleted successfully'
    ]);
    return $response;
  }

  private function validateAppointment(Array $input): Bool
  {
    if (! isset($input['doctor_ci']) ||
        strlen($input['doctor_ci']) < 5) {
      return false;
    }
    if (! isset($input['patient_ci']) ||
        strlen($input['patient_ci']) < 5) {
      return false;
    }
    if (! isset($input['day'])) {
      return false;
    }
    if (! isset($input['hour'])) {
      return false;
    }
    return true;
  }
}


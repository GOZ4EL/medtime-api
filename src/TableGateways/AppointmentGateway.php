<?php
namespace Src\TableGateways;

class AppointmentGateWay {

  private $db = null;

  public function __construct(\PDO $db)
  {
    $this->db = $db;
  }

  public function findAll(): Array
  {
    $statement = "
      SELECT *
      FROM Appointment
        ORDER BY id DESC;
    ";

    try {
      $statement = $this->db->query($statement);
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function find($id): Array
  {
    $statement = "
      SELECT *
      FROM Appointment
      WHERE id = ?;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($id));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function insert(Array $input): Int
  {
    $statement = "
      INSERT INTO Appointment
        (doctor_ci, patient_ci, day, hour)
      VALUES
        (:doctor_ci, :patient_ci, :day, :hour);
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'doctor_ci' => $input['doctor_ci'],
        'patient_ci' => $input['patient_ci'],
        'day' => $input['day'],
        'hour' => $input['hour'],
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function update($id, Array $input): Int
  {
    $statement = "
      UPDATE Appointment
      SET
        day = :day,
        hour = :hour
      WHERE id = :id;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array(
        'id' => (int) $id,
        'day' => $input['day'],
        'hour' => $input['hour'],
      ));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function delete($id): Int
  {
    $statement = "
      DELETE
      FROM Appointment
      WHERE id = :id;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array('ci' => $id));
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function findByDoctorCi($doctor_ci)
  {
    $statement = "
      SELECT *
      FROM Appointment
      WHERE doctor_ci = ?;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($doctor_ci));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC)[0];
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
 
  }

  public function findByPatientCi($patient_ci)
  {
    $statement = "
      SELECT *
      FROM Appointment
      WHERE patient_ci = ?;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute(array($patient_ci));
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC)[0];
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
 
  }
}


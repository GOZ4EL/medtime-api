<?php
namespace Src\Reports;

use Src\Reports\FPDF\FPDF;
require("FPDF/fpdf.php");

class Reports {
  private $db = null;

  public function __construct(\PDO $db) {
    $this->db = $db;
  }

  public function countAllDoctors() {
    $query = "COUNT(SELECT * FROM Doctor);";
    
  }

  public function showReport() {
    $pdf = new PDF(); 
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    $pdf->Cell(190, 20, "Reporte de doctores", 0, 1, 'C', 0);
    $pdf->Ln(12.5);

    $statement = "
      SELECT a.name AS especialidad, COUNT(b.id) AS total
      FROM Speciality a
        JOIN Specialization b ON
          a.name=b.speciality_name";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->cell(195, 10, 'Total de doctores en cada especialidad', 0, 1, 'C', 0);
    
    foreach($result as $key => $value) {
      $pdf->Cell(95, 10, $value['especialidad'], 1, 0, 'C', 0);
      $pdf->Cell(95, 10, $value['total'], 1, 1, 'C', 0);
    }
    $pdf->Ln(10);

    $statement = "
      SELECT 
        CONCAT(d.firstname, ' ', d.lastname) AS doctor, d.cost,
        s.speciality_name AS speciality
      FROM Specialization s
        JOIN Doctor d
          ON s.doctor_ci=d.ci
      WHERE d.cost > 20
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(187, 10, "Doctores con precio de consulta mayor a 20$", 0, 1, 'C', 0);
    foreach($result as $key => $value) {
      $pdf->Cell(64, 10, $value['doctor'], 1, 0, 'C', 0);
      $pdf->Cell(64, 10, strval($value['cost']) . ' $', 1, 0, 'C', 0);
      $pdf->Cell(64, 10, $value['speciality'], 1, 1, 'C', 0);
    }
    $pdf->Ln(10);

    $statement = "
      SELECT 
        CONCAT(d.firstname, ' ', d.lastname) AS doctor, d.cost,
        s.speciality_name AS speciality
      FROM Specialization s
        JOIN Doctor d
          ON s.doctor_ci=d.ci
      WHERE d.cost < 15
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(187, 10, "Doctores con precio de consulta menor a 15$", 0, 1, 'C', 0);
    foreach($result as $key => $value) {
      $pdf->Cell(64, 10, $value['doctor'], 1, 0, 'C', 0);
      $pdf->Cell(64, 10, strval($value['cost']) . ' $', 1, 0, 'C', 0);
      $pdf->Cell(64, 10, $value['speciality'], 1, 1, 'C', 0);
    }

    $pdf->AddPage();

    $pdf->Cell(190, 20, "Totales", 0, 1, 'C', 0);
    $pdf->Ln(12.5);

    $statement = "
      SELECT *
      FROM Doctor
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(95, 10, "Total de doctores", 1, 0, 'C', 0);
    $pdf->Cell(25, 10, count($result), 0, 1, 'C', 0);

    $statement = "
      SELECT *
      FROM Patient
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(95, 10, "Total de pacientes", 1, 0, 'C', 0);
    $pdf->Cell(25, 10, count($result), 0, 1, 'C', 0);

    $statement = "
      SELECT *
      FROM Speciality
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(95, 10, "Total de especialidades", 1, 0, 'C', 0);
    $pdf->Cell(25, 10, count($result), 0, 1, 'C', 0);
    
    $statement = "
      SELECT *
      FROM Appointment
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(95, 10, "Total de citas", 1, 0, 'C', 0);
    $pdf->Cell(25, 10, count($result), 0, 1, 'C', 0);

    $statement = "
      SELECT *
      FROM Appointment
      WHERE status = 'cancelled'
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(95, 10, "Citas canceladas", 1, 0, 'C', 0);
    $pdf->Cell(25, 10, count($result), 0, 1, 'C', 0);

    $statement = "
      SELECT *
      FROM Appointment
      WHERE status = 'done'
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(95, 10, "Citas completadas", 1, 0, 'C', 0);
    $pdf->Cell(25, 10, count($result), 0, 1, 'C', 0);

    $statement = "
      SELECT *
      FROM Appointment
      WHERE status = 'active'
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(95, 10, "Citas en espera", 1, 0, 'C', 0);
    $pdf->Cell(25, 10, count($result), 0, 1, 'C', 0);

    $statement = "
      SELECT AVG(cost) AS cost
      FROM Doctor
    ";
    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
    $pdf->Cell(95, 10, "Promedio del precio de consulta", 1, 0, 'C', 0);
    $pdf->Cell(25, 10, strval(round($result[0]['cost'], 2)) . ' $', 0, 1, 'C', 0);


    $pdf->output();
  }

}

class PDF extends FPDF
{
  // Page header
  function Header()
  {
    // Logo
    //$this->Image('MEDTIME.png',10,6,30);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    $this->Cell(62);
    // Title
    $this->Cell(70,10,'MEDTIME',0,0,'C');
    // Line break
    $this->Ln(20);
  }

  // Page footer
  function Footer()
  {
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'C');
  }
}

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
    $statement = "
      SELECT *
      FROM Doctor
        ORDER BY user_id;
    ";

    $statement = $this->db->query($statement);
    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

    $pdf = new PDF(); 
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    $pdf->Cell(70, 10, "Total de doctores", 1, 0, 'C', 0);
    $pdf->Cell(20, 10, count($result), 0, 1, 'C', 0);
    
   foreach($result as $key => $value) {
      $pdf->Cell(50, 10, $value['ci'], 1, 0, 'C', 0);
      $pdf->Cell(50, 10, $value['firstname'], 1, 0, 'C', 0);
      $pdf->Cell(50, 10, $value['lastname'], 1, 0, 'C', 0);
      $pdf->Cell(50, 10, $value['cost'], 1, 1, 'C', 0);
    }

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
    $this->Cell(70,10,'Reporte de Doctores',0,0,'C');
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

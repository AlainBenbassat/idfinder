<?php

use CRM_Idfinder_ExtensionUtil as E;
require_once  E::path('vendor/autoload.php');

class CRM_Idfinder_BAO_Processor {
  private PhpOffice\PhpSpreadsheet\Spreadsheet $inputFile;
  private PhpOffice\PhpSpreadsheet\Spreadsheet $outputFile;

  // TODO: now hardcoded, but should be configurable
  private array $columnHeadingSynonyms = [
    'contact_id' => ['id', 'contactnummer'],
    'first_name' => ['firstname', 'voornaam'],
    'last_name' => ['lastname', 'achternaam', 'naam', 'familienaam'],
    'email' => ['emailadres', 'e-mail'],
  ];

  private array $columnHeadings = [];

  public function __construct(string $inputFileName) {
    $this->inputFile = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
    $this->outputFile = clone $this->inputFile;
  }

  public function process() {
    $this->findContactIdColumn();
    $this->findFirstNameColumn();
    $this->findLastNameColumn();
    $this->findEmailColumn();

    $this->fillIds();
  }

  public function getOutput() {
    $fileName = 'idfinder_' . date('Ymd_His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->outputFile, "Xlsx");
    $writer->save('php://output');

    CRM_Utils_System::civiExit();
  }

  public function saveAs(string $fileName) {
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->outputFile, "Xlsx");
    $writer->save($fileName);
  }

  private function fillIds() {
    $worksheet = $this->outputFile->getActiveSheet();

    for ($i = 1; $i < 65000; $i++) {
      $id = $worksheet->getCell($this->columnHeadings['contact_id'] . $i)->getValue();
      if (!empty($id)) {
        continue; // id already exists
      }

      $firstName = $worksheet->getCell($this->columnHeadings['first_name'] . $i)->getValue();
      $lastName = $worksheet->getCell($this->columnHeadings['last_name'] . $i)->getValue();
      $email = $worksheet->getCell($this->columnHeadings['email'] . $i)->getValue();

      if (empty($firstName) && empty($lastName) && empty($email)) {
        break; // no more data rows
      }

      if (!empty($firstName) && !empty($lastName) && !empty($email)) {
        $id = CRM_Idfinder_BAO_Contact::get($firstName, $lastName, $email);
        if ($id) {
          $worksheet->setCellValue($this->columnHeadings['contact_id'] . $i, $id);
        }
      }
    }
  }

  private function findContactIdColumn() {
    $columnHeading = $this->findColumnHeading('contact_id');
    if (empty($columnHeading)) {
      $columnHeading = $this->addColumnContactId();
    }

    $this->columnHeadings['contact_id'] = $columnHeading;
  }

  private function findFirstNameColumn() {
    $columnHeading = $this->findColumnHeading('first_name');
    if (empty($columnHeading)) {
      throw new Exception(E::ts("Could not find first name column heading."));
    }

    $this->columnHeadings['first_name'] = $columnHeading;
  }

  private function findLastNameColumn() {
    $columnHeading = $this->findColumnHeading('last_name');
    if (empty($columnHeading)) {
      throw new Exception(E::ts("Could not find last name column heading."));
    }

    $this->columnHeadings['last_name'] = $columnHeading;
  }

  private function findEmailColumn() {
    $columnHeading = $this->findColumnHeading('email');
    if (empty($columnHeading)) {
      throw new Exception(E::ts("Could not find email column heading."));
    }

    $this->columnHeadings['email'] = $columnHeading;
  }

  private function findColumnHeading(string $columnName): ?string {
    $worksheet = $this->outputFile->getActiveSheet();
    for ($i = 1; $i <= 255; $i++) {
      $coord = PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i) . 1;
      $cell = $worksheet->getCell($coord);
      $cellValue = $cell->getValue();
      if (empty($cellValue)) {
        break;
      }

      if ($this->nameMatches($columnName, $cellValue)) {
        return $cell->getColumn();
      }
      elseif (empty($cellValue)) {
        return null;
      }
    }

    return null;
  }

  private function nameMatches(string $name, string $cellValue): bool {
    if (strtolower($name) == strtolower($cellValue)) {
      return TRUE;
    }

    if (empty($this->columnHeadingSynonyms[$name])) {
      return FALSE;
    }

    foreach ($this->columnHeadingSynonyms[$name] as $synonym) {
      if (strtolower($synonym) == strtolower($cellValue)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  private function addColumnContactId() {
    $worksheet = $this->outputFile->getActiveSheet();
    $worksheet->insertNewColumnBefore('A', 1);
    $worksheet->setCellValue('A1', 'contact_id');

    return 'A';
  }

}

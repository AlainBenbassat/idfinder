<?php

use CRM_Idfinder_ExtensionUtil as E;
require_once  E::path('vendor/autoload.php');

class CRM_Idfinder_BAO_Processor {
  private PhpOffice\PhpSpreadsheet\Spreadsheet $inputFile;
  private PhpOffice\PhpSpreadsheet\Spreadsheet $outputFile;
  private string $outputFileName;

  // TODO: now hardcoded, but should be configurable
  private array $columnHeadingSynonyms = [
    'contact_id' => ['id', 'contactnummer'],
    'first_name' => ['firstname', 'voornaam'],
    'last_name' => ['lastname', 'achternaam', 'naam', 'familienaam'],
    'email' => ['emailadres', 'e-mail'],
  ];

  private array $columnHeadings = [];

  public function __construct(string $inputFileName, string $outputFileName) {
    if ($inputFileName == $outputFileName) {
      throw new Exception(E::ts("Input and output file names must be different."));
    }

    $this->outputFileName = $outputFileName;

    $this->inputFile = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
    $this->outputFile = clone $this->inputFile;
  }

  public function process() {
    $this->findContactIdColumn();
    $this->findFirstNameColumn();
    $this->findLastNameColumn();
    $this->findEmailColumn();

    $this->fillIds();

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->outputFile, "Xlsx");
    $writer->save($this->outputFileName);
  }

  private function fillIds() {
    $worksheet = $this->outputFile->getActiveSheet();

    foreach ($worksheet->getRowIterator() as $row) {
      $id = $worksheet->getCellByColumnAndRow($this->columnHeadings['contact_id'], $row->getRowIndex())->getValue();
      if (!empty($id)) {
        continue; // id already exists
      }

      $firstName = $worksheet->getCellByColumnAndRow($this->columnHeadings['first_name'], $row->getRowIndex())->getValue();
      $lastName = $worksheet->getCellByColumnAndRow($this->columnHeadings['last_name'], $row->getRowIndex())->getValue();
      $email = $worksheet->getCellByColumnAndRow($this->columnHeadings['email'], $row->getRowIndex())->getValue();

      if (empty($firstName) && empty($lastName) && empty($email)) {
        break; // no more data rows
      }

      if (!empty($firstName) && !empty($lastName) && !empty($email)) {
        $id = CRM_Idfinder_BAO_Contact::get($firstName, $lastName, $email);
        if ($id) {
          $worksheet->setCellValue($this->columnHeadings['contact_id'], $id);
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
      $cell = $worksheet->getCell(PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i) . 1);
      $cellValue = $cell->getValue();
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
    $worksheet->insertNewRowBefore(1, 1);
    $worksheet->setCellValue('A1', 'contact_id');

    return 'A';
  }

}

<?php

use CRM_Idfinder_ExtensionUtil as E;

class CRM_Idfinder_Form_Upload extends CRM_Core_Form {
  public function buildQuickForm(): void {
    $this->setTitle("Contact ID Finder");
    $this->add('File', 'uploadFile', E::ts('Excel file'), ['size' => 30, 'maxlength' => 255], TRUE);
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
    ]);

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess(): void {
    $values = $this->exportValues();

    // get the selected file
    $tmpFileName = $this->_submitFiles['uploadFile']['tmp_name'];
    if (!$tmpFileName) {
      CRM_Core_Session::setStatus(E::ts("Could not read %1. File too big?", $this->_submitFiles['uploadFile']['name']), 'Error', 'error');
    }
    else {
      $idFinderProcessor = new CRM_Idfinder_BAO_Processor($tmpFileName);
      $idFinderProcessor->process();
      $idFinderProcessor->getOutput();
    }

    parent::postProcess();
  }

  public function getColorOptions(): array {
    $options = [
      '' => E::ts('- select -'),
      '#f00' => E::ts('Red'),
      '#0f0' => E::ts('Green'),
      '#00f' => E::ts('Blue'),
      '#f0f' => E::ts('Purple'),
    ];
    foreach (['1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e'] as $f) {
      $options["#{$f}{$f}{$f}"] = E::ts('Grey (%1)', [1 => $f]);
    }
    return $options;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames(): array {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}

<?php

use CRM_Idfinder_ExtensionUtil as E;

class CRM_Idfinder_Form_Settings extends CRM_Core_Form {
  private CRM_Idfinder_BAO_Settings $settings;

  public function __construct($state = NULL, $action = CRM_Core_Action::NONE, $method = 'post', $name = NULL) {
    $this->settings = new CRM_Idfinder_BAO_Settings();

    parent::__construct($state, $action, $method, $name);
  }

  public function buildQuickForm(): void {
    $this->setTitle(E::ts('ID Finder - settings'));

    $this->addFormFields();
    $this->setFormFieldsDefaultValues();
    $this->addFormButtons();

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess(): void {
    $values = $this->exportValues();

    $this->settings->setContactIdSynonyms($values['contactIdSynonyms']);
    $this->settings->setFirstNameSynonyms($values['firstNameSynonyms']);
    $this->settings->setLastNameSynonyms($values['lastNameSynonyms']);
    $this->settings->setEmailSynonyms($values['emailSynonyms']);

    CRM_Core_Session::setStatus(E::ts('Settings saved'), E::ts('Settings'), 'success');

    parent::postProcess();
  }

  private function addFormFields(): void {
    $textFieldSize = 80;

    $this->add('text', 'contactIdSynonyms', E::ts('Contact ID Synonyms (comma separated list'), ['size' => $textFieldSize], TRUE);
    $this->add('text', 'firstNameSynonyms', E::ts('First Name Synonyms (comma separated list'), ['size' => $textFieldSize], TRUE);
    $this->add('text', 'lastNameSynonyms', E::ts('Last Name Synonyms (comma separated list'), ['size' => $textFieldSize], TRUE);
    $this->add('text', 'emailSynonyms', E::ts('Email Synonyms (comma separated list'), ['size' => $textFieldSize], TRUE);
  }

  private function setFormFieldsDefaultValues() {
    $defaults = [];

    $defaults['contactIdSynonyms'] = $this->settings->getContactIdSynonyms();
    $defaults['firstNameSynonyms'] = $this->settings->getFirstNameSynonyms();
    $defaults['lastNameSynonyms'] = $this->settings->getLastNameSynonyms();
    $defaults['emailSynonyms'] = $this->settings->getEmailSynonyms();

    $this->setDefaults($defaults);
  }

  private function addFormButtons() {
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ],
    ]);
  }

  private function getRenderableElementNames(): array {
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

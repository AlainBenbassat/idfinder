<?php

class CRM_Idfinder_BAO_Settings {
  public const settingContactIdSynonyms = 'idfinder_contact_id_synonyms';
  public const settingFirstNameSynonyms = 'idfinder_first_name_synonyms';
  public const settingLastNameSynonyms = 'idfinder_last_name_synonyms';
  public const settingEmailSynonyms = 'idfinder_email_synonyms';

  public function getContactIdSynonyms() {
    return Civi::settings()->get( self::settingContactIdSynonyms);
  }

  public function getFirstNameSynonyms() {
    return Civi::settings()->get( self::settingFirstNameSynonyms);
  }

  public function getLastNameSynonyms() {
    return Civi::settings()->get( self::settingLastNameSynonyms);
  }

  public function getEmailSynonyms() {
    return Civi::settings()->get( self::settingEmailSynonyms);
  }

  public function setContactIdSynonyms($synonyms) {
    Civi::settings()->set( self::settingContactIdSynonyms, $synonyms);
  }

  public function setFirstNameSynonyms($synonyms) {
    Civi::settings()->set( self::settingFirstNameSynonyms, $synonyms);
  }

  public function setLastNameSynonyms($synonyms) {
    Civi::settings()->set( self::settingLastNameSynonyms, $synonyms);
  }

  public function setEmailSynonyms($synonyms) {
    Civi::settings()->set( self::settingEmailSynonyms, $synonyms);
  }
}

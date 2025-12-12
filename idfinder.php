<?php

require_once 'idfinder.civix.php';

use CRM_Idfinder_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function idfinder_civicrm_config(&$config): void {
  _idfinder_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function idfinder_civicrm_install(): void {
  _idfinder_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function idfinder_civicrm_enable(): void {
  CRM_Idfinder_BAO_Navigation::createIfNotExists();

  _idfinder_civix_civicrm_enable();
}

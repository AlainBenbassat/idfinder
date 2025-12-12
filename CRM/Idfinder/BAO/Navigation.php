<?php

use CRM_Idfinder_ExtensionUtil as E;

class CRM_Idfinder_BAO_Navigation {
  public static function createIfNotExists() {
    if (self::exists()) {
      return;
    }

    $topMenu = self::createTopMenu();
    self::createSettingsMenu($topMenu['id']);
    self::createUploadMenu($topMenu['id']);
  }

  private static function exists() {
    $navigation = \Civi\Api4\Navigation::get(FALSE)
      ->addWhere('name', '=', 'idfinder')
      ->execute()
      ->first();

    return !empty($navigation);
  }

  private static function createTopMenu() {
    return \Civi\Api4\Navigation::create(FALSE)
      ->addValue('domain_id', 1)
      ->addValue('label', E::ts('ID Finder'))
      ->addValue('name', 'idfinder')
      ->addValue('url', null)
      ->addValue('permission', ['administer CiviCRM'])
      ->addValue('parent_id', self::getAdministerMenuId())
      ->addValue('is_active', TRUE)
      ->execute()
      ->first();
  }

  private static function createSettingsMenu(int $parentId) {
    return \Civi\Api4\Navigation::create(FALSE)
      ->addValue('domain_id', 1)
      ->addValue('label', E::ts('Settings'))
      ->addValue('name', 'idfinder_settings')
      ->addValue('url', 'civicrm/idfinder/settings')
      ->addValue('permission', ['administer CiviCRM'])
      ->addValue('parent_id', $parentId)
      ->addValue('is_active', TRUE)
      ->execute()
      ->first();
  }

  private static function createUploadMenu(int $parentId) {
    return \Civi\Api4\Navigation::create(FALSE)
      ->addValue('domain_id', 1)
      ->addValue('label', E::ts('Upload Excel'))
      ->addValue('name', 'idfinder_upload')
      ->addValue('url', 'civicrm/idfinder/upload')
      ->addValue('permission', ['administer CiviCRM'])
      ->addValue('parent_id', $parentId)
      ->addValue('is_active', TRUE)
      ->execute()
      ->first();
  }

  private static function getAdministerMenuId() {
    $menu = \Civi\Api4\Navigation::get(FALSE)
      ->addWhere('name', '=', 'Administer')
      ->execute()
      ->first();

    return $menu['id'];
  }
}

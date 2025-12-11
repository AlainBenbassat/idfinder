<?php

class CRM_Idfinder_BAO_Contact {
  public static function get(string $firstName, string $lastName, string $email) {
    $contact = \Civi\Api4\Contact::get(FALSE)
      ->addSelect('id', 'first_name', 'last_name', 'email.email')
      ->addWhere('first_name', '=', $firstName)
      ->addWhere('last_name', '=', $lastName)
      ->addJoin('Email AS email', 'INNER', ['id', '=', 'email.contact_id'])
      ->addWhere('email.email', '=', $email)
      ->addWhere('is_deleted', '=', FALSE)
      ->execute()
      ->first();

    if (empty($contact)) {
      // swap first and last name
      $contact = \Civi\Api4\Contact::get(FALSE)
        ->addSelect('id', 'first_name', 'last_name', 'email.email')
        ->addWhere('first_name', '=', $lastName)
        ->addWhere('last_name', '=', $firstName)
        ->addJoin('Email AS email', 'INNER', ['id', '=', 'email.contact_id'])
        ->addWhere('email.email', '=', $email)
        ->addWhere('is_deleted', '=', FALSE)
        ->execute()
        ->first();
    }

    if (!empty($contact)) {
      return $contact['id'];
    }

    return NULL;
  }
}

<?php

use Drupal\webform\Entity\Webform;

$storage = \Drupal::entityTypeManager()->getStorage('webform_submission');
$storage->delete($storage->loadMultiple());
foreach (['contact_form', 'order_form'] as $id) {
  $webform = Webform::load($id);
  $webform->setSetting('confirmation_back_label', 'Повернутися до форми');
  $webform->save();
  \Drupal::languageManager()->getLanguageConfigOverride('en', "webform.webform.$id")
    ->set('settings.confirmation_back_label', 'Back to form')->save();
}
echo 'submissions left: ' . count($storage->loadMultiple()) . PHP_EOL;

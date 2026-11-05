<?php

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;

if (!NodeType::load('page')) {
  NodeType::create(['type' => 'page', 'name' => 'Сторінка', 'description' => 'Прості сторінки сайту.', 'new_revision' => TRUE, 'display_submitted' => FALSE])->save();
}
if (!FieldStorageConfig::loadByName('node', 'body')) {
  FieldStorageConfig::create(['field_name' => 'body', 'entity_type' => 'node', 'type' => 'text_with_summary', 'cardinality' => 1, 'translatable' => TRUE])->save();
}
if (!FieldConfig::loadByName('node', 'page', 'body')) {
  FieldConfig::create(['field_name' => 'body', 'entity_type' => 'node', 'bundle' => 'page', 'label' => 'Вміст', 'translatable' => TRUE, 'settings' => ['display_summary' => FALSE]])->save();
}
$repository = \Drupal::service('entity_display.repository');
$repository->getFormDisplay('node', 'page', 'default')->setComponent('body', ['type' => 'text_textarea_with_summary', 'weight' => 5])->save();
$repository->getViewDisplay('node', 'page', 'default')->setComponent('body', ['type' => 'text_default', 'label' => 'hidden', 'weight' => 0])->removeComponent('links')->save();
echo "page type ready\n";

<?php

use Drupal\block\Entity\Block;

foreach (Block::loadMultiple() as $block) {
  if ($block->getTheme() === 'yasnyi') {
    $block->delete();
  }
}
$place = function ($id, $plugin, $region, $weight, $settings = [], $visibility = []) {
  Block::create(['id' => $id, 'theme' => 'yasnyi', 'plugin' => $plugin, 'region' => $region, 'weight' => $weight,
    'settings' => ['label_display' => '0'] + $settings, 'visibility' => $visibility])->save();
};
$onNode = fn($id) => ['request_path' => ['id' => 'request_path', 'negate' => FALSE, 'pages' => "/node/$id"]];
$place('yasnyi_menu', 'system_menu_block:main', 'primary_menu', 0, ['id' => 'system_menu_block:main', 'label' => 'Main menu', 'provider' => 'system', 'level' => 1, 'depth' => 1, 'expand_all_items' => FALSE]);
$place('yasnyi_language', 'language_block:language_interface', 'language', 0, ['id' => 'language_block:language_interface', 'label' => 'Language', 'provider' => 'language']);
$place('yasnyi_messages', 'system_messages_block', 'highlighted', 0, ['id' => 'system_messages_block', 'label' => 'Messages', 'provider' => 'system']);
$place('yasnyi_content', 'system_main_block', 'content', 0, ['id' => 'system_main_block', 'label' => 'Content', 'provider' => 'system']);
$place('yasnyi_contact_form', 'webform_block', 'content', 10, ['id' => 'webform_block', 'label' => 'Contact form', 'provider' => 'webform', 'webform_id' => 'contact_form', 'default_data' => '', 'redirect' => FALSE], $onNode(6));
$place('yasnyi_order_form', 'webform_block', 'content', 10, ['id' => 'webform_block', 'label' => 'Order form', 'provider' => 'webform', 'webform_id' => 'order_form', 'default_data' => '', 'redirect' => FALSE], $onNode(7));

\Drupal::configFactory()->getEditable('system.site')->set('name', 'Ясний Дім')->save();
\Drupal::languageManager()->getLanguageConfigOverride('en', 'system.site')->set('name', 'Yasnyi Dim')->save();
echo 'blocks: ' . implode(',', array_keys(array_filter(Block::loadMultiple(), fn($b) => $b->getTheme() === 'yasnyi'))) . PHP_EOL;

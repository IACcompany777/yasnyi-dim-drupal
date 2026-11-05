<?php

use Drupal\block\Entity\Block;
use Drupal\Core\Serialization\Yaml;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\language\Entity\ContentLanguageSettings;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\webform\Entity\Webform;

// Languages.
if (!ConfigurableLanguage::load('en')) {
  ConfigurableLanguage::createFromLangcode('en')->save();
}
$negotiation = \Drupal::configFactory()->getEditable('language.negotiation');
$negotiation->set('url.prefixes', ['uk' => '', 'en' => 'en'])->save();
foreach ([['node', 'page'], ['menu_link_content', 'menu_link_content']] as [$type, $bundle]) {
  ContentLanguageSettings::loadByEntityTypeBundle($type, $bundle)
    ->setLanguageAlterable(TRUE)->setDefaultLangcode('site_default')->save();
  \Drupal::service('content_translation.manager')->setEnabled($type, $bundle, TRUE);
}
\Drupal::service('entity_field.manager')->clearCachedFieldDefinitions();
\Drupal::service('router.builder')->rebuild();

// Theme.
\Drupal::service('theme_installer')->install(['yasnyi']);
\Drupal::configFactory()->getEditable('system.theme')->set('default', 'yasnyi')->save();

$hero = fn($eyebrow, $title, $lead, $button = '') => "<section class=\"hero hero--small\"><div class=\"wrap\"><p class=\"eyebrow\">$eyebrow</p><h1>$title</h1><p class=\"lead\">$lead</p>$button</div></section>";
$cards = fn($title, $items) => "<section class=\"section\"><div class=\"wrap\"><h2>$title</h2><div class=\"cards\">" . implode('', array_map(fn($i) => "<article class=\"card\"><h3>{$i[0]}</h3><p>{$i[1]}</p>" . (isset($i[2]) ? "<p class=\"price\">{$i[2]}</p>" : '') . '</article>', $items)) . '</div></div></section>';
$cta = fn($title, $text, $label, $href) => "<section class=\"section\"><div class=\"wrap\"><div class=\"cta\"><h2>$title</h2><p>$text</p><a class=\"button\" href=\"$href\">$label</a></div></div></section>";
$faq = fn($title, $items) => "<section class=\"section\"><div class=\"wrap\"><h2>$title</h2>" . implode('', array_map(fn($i) => "<details class=\"faq-item\"><summary>{$i[0]}</summary><p>{$i[1]}</p></details>", $items)) . '</div></section>';

$pages = [
  'home' => [
    'uk' => ['Головна', '/golovna', str_replace('hero hero--small', 'hero', $hero('Прибирання у Вінниці', 'Чистий дім без зайвого клопоту', 'Прибирання квартир, будинків і невеликих офісів. Оберіть послугу, залиште заявку, а час узгодимо окремо.', '<a class="button" href="/zamovyty">Залишити заявку</a>'))
      . $cards('Що ми робимо', [['Квартири', 'Регулярне й генеральне прибирання.'], ['Офіси', 'Чисті робочі місця та спільні зони.'], ['Після ремонту', 'Прибирання пилу та слідів будівельних робіт.']])
      . '<section class="section"><div class="wrap"><h2>Як це працює</h2><ol class="steps"><li>Ви залишаєте заявку.</li><li>Ми узгоджуємо обсяг і час.</li><li>Команда приїжджає й прибирає.</li></ol></div></section>'
      . $cta('Потрібне прибирання?', 'Опишіть завдання, і ми підкажемо, що підійде.', 'Замовити', '/zamovyty')],
    'en' => ['Home', '/home', str_replace('hero hero--small', 'hero', $hero('Cleaning in Vinnytsia', 'A clean home without the hassle', 'Cleaning for apartments, houses, and small offices. Choose a service, send a request, and we will arrange the time.', '<a class="button" href="/en/order">Send a request</a>'))
      . $cards('What we do', [['Apartments', 'Regular and deep cleaning.'], ['Offices', 'Clean workspaces and shared areas.'], ['After renovation', 'Removing dust and traces of building work.']])
      . '<section class="section"><div class="wrap"><h2>How it works</h2><ol class="steps"><li>You send a request.</li><li>We agree on the scope and time.</li><li>The team arrives and cleans.</li></ol></div></section>'
      . $cta('Need cleaning?', 'Describe the job, and we will suggest what fits.', 'Order', '/en/order')],
  ],
  'about' => [
    'uk' => ['Про нас', '/pro-nas', $hero('Про нас', 'Прибираємо так, як домовилися', 'Ясний Дім — концепт сайту клінінгової служби для малого бізнесу.')
      . '<section class="section"><div class="wrap prose"><h2>Наш підхід</h2><p>Спершу уточнюємо, що саме потрібно зробити, а потім працюємо за погодженим списком.</p><p>Ви заздалегідь знаєте обсяг робіт і час приїзду.</p></div></section>'
      . $cta('Маєте запитання?', 'Напишіть нам через форму на сторінці контактів.', 'Контакти', '/kontakty')],
    'en' => ['About', '/about', $hero('About', 'We clean the way we agreed', 'Yasnyi Dim is a site concept for a small cleaning business.')
      . '<section class="section"><div class="wrap prose"><h2>Our approach</h2><p>First we clarify what needs to be done, and then we follow the agreed list.</p><p>You know the scope of work and arrival time in advance.</p></div></section>'
      . $cta('Have a question?', 'Write to us using the form on the contact page.', 'Contact', '/en/contact')],
  ],
  'services' => [
    'uk' => ['Послуги', '/poslugy', $hero('Послуги', 'Оберіть, що потрібно прибрати', 'Кожну послугу можна доповнити окремими завданнями.')
      . $cards('Основні послуги', [['Регулярне прибирання', 'Пил, підлога, кухня та ванна кімната.'], ['Генеральне прибирання', 'Детальне прибирання всіх кімнат.'], ['Після ремонту', 'Пил, бруд і сліди будівельних робіт.'], ['Прибирання офісу', 'Робочі місця, кухня та санвузли.'], ['Миття вікон', 'Вікна, рами й підвіконня.'], ['Хімчистка меблів', 'Дивани, крісла та матраци.']])
      . $cta('Не знаєте, що обрати?', 'Опишіть приміщення, і ми порадимо.', 'Залишити заявку', '/zamovyty')],
    'en' => ['Services', '/services', $hero('Services', 'Choose what needs cleaning', 'You can add separate tasks to any service.')
      . $cards('Main services', [['Regular cleaning', 'Dust, floors, kitchen, and bathroom.'], ['Deep cleaning', 'Detailed cleaning of every room.'], ['After renovation', 'Dust, dirt, and traces of building work.'], ['Office cleaning', 'Workspaces, kitchen, and bathrooms.'], ['Window cleaning', 'Windows, frames, and sills.'], ['Upholstery cleaning', 'Sofas, armchairs, and mattresses.']])
      . $cta('Not sure what to choose?', 'Describe the space, and we will advise you.', 'Send a request', '/en/order')],
  ],
  'prices' => [
    'uk' => ['Ціни', '/tsiny', $hero('Ціни', 'Приклад цін', 'Ціни на цій сторінці наведено для прикладу.')
      . $cards('Пакети', [['Базовий', 'Регулярне прибирання квартири.', 'від 900 грн'], ['Детальний', 'Генеральне прибирання квартири.', 'від 2 200 грн'], ['Офіс', 'Прибирання невеликого офісу.', 'від 1 500 грн']])
      . '<div class="wrap"><p class="note">Концепт сайту: це не комерційна пропозиція.</p></div>'],
    'en' => ['Prices', '/prices', $hero('Prices', 'Sample prices', 'The prices on this page are examples.')
      . $cards('Packages', [['Basic', 'Regular apartment cleaning.', 'from 900 UAH'], ['Detailed', 'Deep apartment cleaning.', 'from 2,200 UAH'], ['Office', 'Small office cleaning.', 'from 1,500 UAH']])
      . '<div class="wrap"><p class="note">Site concept: this is not a commercial offer.</p></div>'],
  ],
  'faq' => [
    'uk' => ['Питання', '/pytannia', $hero('Питання', 'Часті запитання', 'Короткі відповіді на те, що зазвичай запитують.')
      . $faq('Перед замовленням', [['Чи потрібно бути вдома?', 'Це можна узгодити окремо перед прибиранням.'], ['Хто привозить засоби?', 'Це узгоджується разом з обсягом робіт.'], ['Чи можна змінити час?', 'Так, напишіть про це якомога раніше.']])],
    'en' => ['FAQ', '/faq', $hero('FAQ', 'Frequently asked questions', 'Short answers to common questions.')
      . $faq('Before you order', [['Do I need to be home?', 'This can be agreed separately before the cleaning.'], ['Who brings the supplies?', 'This is agreed along with the scope of work.'], ['Can I change the time?', 'Yes, just let us know as early as you can.']])],
  ],
  'contact' => [
    'uk' => ['Контакти', '/kontakty', $hero('Контакти', 'Напишіть нам', 'Заповніть форму. Це концепт сайту, тому заявка зберігається лише в цьому тестовому сайті.')],
    'en' => ['Contact', '/contact', $hero('Contact', 'Write to us', 'Fill in the form. This is a site concept, so your message stays only on this test site.')],
  ],
  'order' => [
    'uk' => ['Замовити', '/zamovyty', $hero('Замовлення', 'Залиште заявку', 'Вкажіть послугу й зручну дату. Це концепт сайту, тому заявка зберігається лише в цьому тестовому сайті.')],
    'en' => ['Order', '/order', $hero('Order', 'Send a request', 'Choose a service and a suitable date. This is a site concept, so your request stays only on this test site.')],
  ],
];

$nodes = [];
foreach ($pages as $key => $page) {
  [$title, $alias, $body] = $page['uk'];
  $node = Node::create(['type' => 'page', 'langcode' => 'uk', 'title' => $title, 'status' => 1, 'uid' => 1,
    'body' => ['value' => $body, 'format' => 'full_html'], 'path' => ['alias' => $alias, 'langcode' => 'uk']]);
  $node->save();
  [$title, $alias, $body] = $page['en'];
  $node->addTranslation('en', ['title' => $title, 'status' => 1, 'uid' => 1,
    'body' => ['value' => $body, 'format' => 'full_html'], 'path' => ['alias' => $alias, 'langcode' => 'en']])->save();
  $nodes[$key] = $node;
}
\Drupal::configFactory()->getEditable('system.site')->set('page.front', '/node/' . $nodes['home']->id())->save();

// Menu.
\Drupal::service('plugin.manager.menu.link')->updateDefinition('standard.front_page', ['enabled' => 0]);
foreach (array_keys($pages) as $weight => $key) {
  $link = MenuLinkContent::create(['title' => $pages[$key]['uk'][0], 'menu_name' => 'main', 'langcode' => 'uk',
    'link' => ['uri' => 'entity:node/' . $nodes[$key]->id()], 'weight' => $weight, 'expanded' => FALSE]);
  $link->save();
  $link->addTranslation('en', ['title' => $pages[$key]['en'][0], 'link' => ['uri' => 'entity:node/' . $nodes[$key]->id()], 'menu_name' => 'main'])->save();
}

// Forms.
$contactElements = [
  'name' => ['#type' => 'textfield', '#title' => "Ім'я", '#required' => TRUE],
  'email' => ['#type' => 'email', '#title' => 'Електронна пошта', '#required' => TRUE],
  'message' => ['#type' => 'textarea', '#title' => 'Повідомлення', '#required' => TRUE],
  'actions' => ['#type' => 'webform_actions', '#title' => 'Кнопка', '#submit__label' => 'Надіслати'],
];
$orderElements = [
  'name' => ['#type' => 'textfield', '#title' => "Ім'я", '#required' => TRUE],
  'phone' => ['#type' => 'tel', '#title' => 'Телефон', '#required' => TRUE],
  'service' => ['#type' => 'select', '#title' => 'Послуга', '#required' => TRUE, '#options' => ['regular' => 'Регулярне прибирання', 'deep' => 'Генеральне прибирання', 'renovation' => 'Після ремонту', 'office' => 'Прибирання офісу']],
  'date' => ['#type' => 'date', '#title' => 'Бажана дата', '#required' => TRUE, '#date_date_min' => 'today'],
  'comment' => ['#type' => 'textarea', '#title' => 'Коментар'],
  'actions' => ['#type' => 'webform_actions', '#title' => 'Кнопка', '#submit__label' => 'Залишити заявку'],
];
$forms = [
  'contact_form' => ['Контактна форма', $contactElements, 'Дякуємо! Повідомлення збережено в цьому тестовому сайті.', 'Contact form', 'Thank you! Your message was saved on this test site.',
    ['name' => ['#title' => 'Name'], 'email' => ['#title' => 'Email'], 'message' => ['#title' => 'Message'], 'actions' => ['#title' => 'Button', '#submit__label' => 'Send']]],
  'order_form' => ['Форма замовлення', $orderElements, 'Дякуємо! Заявку збережено в цьому тестовому сайті.', 'Order form', 'Thank you! Your request was saved on this test site.',
    ['name' => ['#title' => 'Name'], 'phone' => ['#title' => 'Phone'], 'service' => ['#title' => 'Service', '#options' => ['regular' => 'Regular cleaning', 'deep' => 'Deep cleaning', 'renovation' => 'After renovation', 'office' => 'Office cleaning']], 'date' => ['#title' => 'Preferred date'], 'comment' => ['#title' => 'Comment'], 'actions' => ['#title' => 'Button', '#submit__label' => 'Send a request']]],
];
foreach ($forms as $id => [$title, $elements, $confirmation, $enTitle, $enConfirmation, $enElements]) {
  $webform = Webform::create(['id' => $id, 'title' => $title, 'langcode' => 'uk', 'status' => 'open', 'elements' => Yaml::encode($elements)]);
  $webform->setSetting('confirmation_type', 'inline');
  $webform->setSetting('confirmation_message', $confirmation);
  $webform->save();
  \Drupal::languageManager()->getLanguageConfigOverride('en', "webform.webform.$id")
    ->set('title', $enTitle)->set('elements', Yaml::encode($enElements))->set('settings.confirmation_message', $enConfirmation)->save();
}


\Drupal::configFactory()->getEditable('system.site')->set('name', 'Ясний Дім')->save();
\Drupal::languageManager()->getLanguageConfigOverride('en', 'system.site')->set('name', 'Yasnyi Dim')->save();

echo 'nodes: ' . implode(',', array_map(fn($n) => $n->id(), $nodes)) . PHP_EOL;

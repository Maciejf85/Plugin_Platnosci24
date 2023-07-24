<?php

namespace Inc\Pages;

use \Inc\Api\SettingsApi;
use \Inc\Api\Callbacks\AdminCallbacks;

class Admin
{

  public $settings;
  public $callbacks;


  public function register()
  {
    add_action('admin_menu', [$this, 'add_admin_pages']);

    $this->settings = new SettingsApi();
    $this->callbacks = new AdminCallbacks();

    $this->setSettings();
    $this->setSections();
    $this->setFields();

    $this->settings->register();
  }

  public function add_admin_pages()
  {
    add_menu_page('GTWPrzelewy24', 'Przelewy24', 'manage_options', 'gtwPrzelewy24', [$this, 'admin_index'], 'dashicons-bank', 110);

    add_submenu_page('gtwPrzelewy24', 'General', 'Ustawienia', 'manage_options', 'gtwPrzelewy24', [$this, 'admin_index'], 110);
    add_submenu_page('gtwPrzelewy24', 'Orders', 'Zamówienia', 'manage_options', 'gtwplatnosci-24-orders', [$this, 'admin_subpages_orders'], 110);
    add_submenu_page('gtwPrzelewy24', 'Raports', 'Raporty', 'manage_options', 'gtwplatnosci-24-raports', [$this, 'admin_subpages_raports'], 110);
  }

  public function admin_index()
  {
    require_once GTW_P24_PLUGIN_PATH . 'templates/payments.php';
  }

  public function admin_subpages_orders()
  {
    require_once GTW_P24_PLUGIN_PATH . 'templates/payments-orders.php';
  }
  public function admin_subpages_raports()
  {
    require_once GTW_P24_PLUGIN_PATH . 'templates/payments-raports.php';
  }

  /**
   * Place where we create subfields
   */

  public function setSettings()
  {
    $args = [
      [
        'option_group' => 'gtw_przelewy24_plugin_settings',
        'option_name' => 'gtw_p24ID',
        'callback' => [$this->callbacks, 'gtwPrzelewy24InputSanitize'],
      ],
      [
        'option_group' => 'gtw_przelewy24_plugin_settings',
        'option_name' => 'gtw_p24CRC',
        'callback' => [$this->callbacks, 'gtwPrzelewy24InputSanitize'],
      ],
      [
        'option_group' => 'gtw_przelewy24_plugin_settings',
        'option_name' => 'gtw_p24Raports',
        'callback' => [$this->callbacks, 'gtwPrzelewy24InputSanitize'],
      ],
      [
        'option_group' => 'gtw_przelewy24_plugin_settings',
        'option_name' => 'gtw_p24Mode',
        'callback' => [$this->callbacks, 'gtwPrzelewy24InputSanitize'],
      ],
    ];

    $this->settings->setSettings($args);
  }

  public function setSections()
  {

    $args = [
      [
        'id' => 'gtw_przelewy24_admin_index',
        'title' => '',
        'callback' => [$this->callbacks, 'gtwPrzelewy24AdminSection'],
        'page' => 'gtwprzelewy24'
      ]
    ];

    $this->settings->setSections($args);
  }

  public function setFields()
  {

    $args = [
      [
        'id' => 'gtw_p24ID',
        'title' => 'ID Sprzedawcy',
        'callback' => [$this->callbacks, 'inputField'],
        'page' => 'gtwprzelewy24',
        'section' => 'gtw_przelewy24_admin_index',
        'args' => [
          'label_for' => 'gtw_p24ID',
          'class' => 'regular-text',
          'placeholder' => ''
        ],
      ],
      [
        'id' => 'gtw_p24CRC',
        'title' => 'CRC Sprzedawcy',
        'callback' => [$this->callbacks, 'inputField'],
        'page' => 'gtwprzelewy24',
        'section' => 'gtw_przelewy24_admin_index',
        'args' => [
          'label_for' => 'gtw_p24CRC',
          'class' => 'regular-text',
          'placeholder' => ''
        ],
      ],
      [
        'id' => 'gtw_p24Raports',
        'title' => 'Klucz do raportów',
        'callback' => [$this->callbacks, 'inputField'],
        'page' => 'gtwprzelewy24',
        'section' => 'gtw_przelewy24_admin_index',
        'args' => [
          'label_for' => 'gtw_p24Raports',
          'class' => 'regular-text',
          'placeholder' => ''
        ],
      ],
      [
        'id' => 'gtw_p24Mode',
        'title' => 'Tryb działania',
        'callback' => [$this->callbacks, 'selectField'],
        'page' => 'gtwprzelewy24',
        'section' => 'gtw_przelewy24_admin_index',
        'args' => [
          'label_for' => 'gtw_p24Mode',
          'class' => 'regular-text',
          'options' => [
            'sandbox' => 'testowy',
            'secure' => 'normalny',
          ]
        ],
      ],
    ];

    $this->settings->setFields($args);
  }
}

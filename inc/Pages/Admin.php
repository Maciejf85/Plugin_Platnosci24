<?php

namespace Inc\Pages;

class Admin 
{
  public function register(){
    add_action( 'admin_menu' , [ $this, 'add_admin_pages' ] );
  }
  
  public function add_admin_pages(){
    add_menu_page('GTWPlatnosci24', 'Płatności24', 'manage_options', 'gtwplatnosci_24', [$this, 'admin_index'],'dashicons-bank', 110 );
     
    add_submenu_page('gtwplatnosci_24', 'General', 'Ustawienia' ,'manage_options', 'gtwplatnosci_24', [$this, 'admin_index'], 110 );
    add_submenu_page('gtwplatnosci_24', 'Orders', 'Zamówienia' ,'manage_options', 'gtwplatnosci-24-settings', [$this, 'admin_subpages'], 110 );
  }

public function admin_index(){
  require_once GTW_P24_PLUGIN_PATH . 'templates/payments.php';
}

public function admin_subpages(){
  require_once GTW_P24_PLUGIN_PATH . 'templates/payments-settings.php';
}

}

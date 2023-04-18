<?php

namespace Inc\Base;

class Enqueue 
{
  public function register()
  {
    add_action('admin_enqueue_scripts', [ $this, 'enqueue' ] );
  }
  
  public function enqueue()
  {
    wp_enqueue_style( 'mypluginstyle', GTW_P24_PLUGIN_URL.'assets/mystyle.css');
    wp_enqueue_script( 'mypluginscripts', GTW_P24_PLUGIN_URL.'assets/myscript.js'); 
  }

  public function admin_index()
  {
    require_once GTW_P24_PLUGIN_PATH . 'templates/payments.php';
  }  
}

<?php
/*
* Plugin Name: GTW-Platnosci24
* Description: A plugin for integrating Wordpress with the Przelewy24 payment system
* Version: 1.0.0
* Requires PHP: 7.4
* Author: Maciej Fiałkowski
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: GTW-Platnosci24
* Domain Path: /languages
*/

if(!defined('ABSPATH')){
  die;
}

if ( ! function_exists('add_action')){
  exit;
}

if(!class_exists('GTWPlatnosci24')){
  class GTWPlatnosci24 {

    private $plugin;

 function __construct(){
  $this->plugin = plugin_basename(__FILE__);
 }

 public function register(){
  add_action('admin_enqueue_scripts', [ $this, 'enqueue' ] );
  add_action( 'admin_menu' , [ $this, 'add_admin_pages' ] );
  add_filter("plugin_action_links_$this->plugin", [ $this, 'settings_link']);
 }

 public function settings_link($links){
  $settings_link = '<a href="admin.php?page=gtwplatnosci_24">Settings</a>';
  array_push($links, $settings_link);
  return $links;
 }

 public function add_admin_pages(){
  add_menu_page('GTWPlatnosci24', 'Płatności24', 'manage_options', 'gtwplatnosci_24', [$this, 'admin_index'],'dashicons-bank', null );
 }

 public function admin_index(){
  // require template
  require_once plugin_dir_path(__FILE__) . 'templates/payments.php';
 }

 public function activate(){}

 public function deactivate(){}
 

 public function enqueue(){
  wp_enqueue_style( 'mypluginstyle', plugins_url('/assets/mystyle.css', __FILE__));
  wp_enqueue_script( 'mypluginscripts', plugins_url('/assets/myscript.js', __FILE__)); 
 }
}

if(class_exists('GTWPlatnosci24')){
  $gtwPlatnosci24 = new GTWPlatnosci24();
  $gtwPlatnosci24->register();
}

// activation
register_activation_hook( __FILE__, [$gtwPlatnosci24,'activate'] );

// deactivation
register_activation_hook( __FILE__, [$gtwPlatnosci24,'deactivate'] );
}
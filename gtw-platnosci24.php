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

if(file_exists( dirname(__FILE__) . '/vendor/autoload.php' )){
  require_once dirname(__FILE__) . '/vendor/autoload.php';
}


// Define CONSTANTS
define( 'GTW_P24_PLUGIN_PATH', plugin_dir_path( (__FILE__) ) );
define( 'GTW_P24_PLUGIN_URL', plugin_dir_url( (__FILE__) ) );
define( 'GTW_P24_PLUGIN_BASENAME', plugin_basename( __FILE__) );

use Inc\Base\Activate;
use Inc\Base\Deactivate;

function activate_gtw_platnosci24_plugin()
{
  Activate::activate();
}
register_activation_hook( __FILE__, 'activate_gtw_platnosci24_plugin' );


function deactivate_gtw_platnosci24_plugin()
{
  Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_gtw_platnosci24_plugin' );


if(class_exists('Inc\\Init')){
  Inc\Init::register_services();
}

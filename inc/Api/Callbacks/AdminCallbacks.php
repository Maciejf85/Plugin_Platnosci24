<?php

namespace Inc\Api\Callbacks;

class AdminCallbacks
{
public function gtw_przelewy24PluginSettings( $input )
{
  return $input;
}

public function gtwPrzelewy24AdminSection( $input )
{
  return (isset($input) ? true : false) ;
}

 public function inputField($args)
 {
  $name = $args['label_for'];
  $class = $args['class'];
  $placeholder = $args['placeholder'];

  $value = esc_attr( get_option($name) );
  echo '<input type="text" class="'. $class .'" name="'. $name .'" value="'. $value .'" placeholder="' . $placeholder . '">';
 }

 public function selectField($args)
 {
  $name = $args['label_for'];
  $class = $args['class'];
  $options = $args['options'];

    echo '<select name="'. $name .'" class="'. $class .'">';
    foreach($options as $key => $value){
      echo '<option value="'. $key . '" ' . selected($key, get_option($name), false ) . '>'. $value . '</option>';
    }
    echo '</select>';       
 }
}

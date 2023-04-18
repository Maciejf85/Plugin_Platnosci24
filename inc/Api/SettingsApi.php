<?php

namespace Inc\Api;

class SettingsApi 
{

  public $settings = [];
  public $sections = [];
  public $fields = [];

  public function setSettings( array $settings){
    $this->settings = $settings;
    return $this;
  }

  public function setSections( array $sections){
    $this->sections = $sections;
    return $this;
  }

  public function setFields( array $fields){
    $this->fields = $fields;
    return $this;
  }

  public function registerCustomFields(){
    foreach($this->settings as $setting){
      register_setting($setting['option_group'], $setting['option_name'], (isset($setting['callback'])? $setting['callback'] : ''));
    }
    
    foreach($this->sections as $section){
      add_settings_section( $section['id'], $section['title'],  (isset($section['callback'])? $section['callback'] : ''), $section['page']);
    }
    
    foreach($this->fields as $field){
      add_settings_field( $field['id'], $field['title'], (isset($field['callback'])? $field['callback'] : ''), $field['page'], 'default', (isset($field['args'])? $field['args'] : '')  );
    }
  }
  
}
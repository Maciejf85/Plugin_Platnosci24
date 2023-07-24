<?php

namespace Inc\Base;

class SettingsLinks
{
  public function register()
  {
    add_filter("plugin_action_links_" . GTW_P24_PLUGIN_BASENAME, [$this, 'settings_link']);
  }

  public function settings_link($links)
  {
    $settings_link = '<a href="admin.php?page=gtwplatnosci_24">Settings</a>';
    array_push($links, $settings_link);
    return $links;
  }
}

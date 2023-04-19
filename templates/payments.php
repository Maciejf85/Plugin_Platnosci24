<div class="wrap">
  <h1 class="wp-heading-inline">Ustawienia</h1>

  <?php settings_errors(); ?>

  <form method="post" action="options.php" class="p24__form">
    <?php 
      settings_fields('gtw_przelewy24_plugin_settings');
      do_settings_sections('gtwprzelewy24');
      submit_button();
    ?>
  </form>
</div>

<?php

use Inc\Base\Methods;
?>

<div class="wrap">
  <h1 class="wp-heading-inline">Ustawienia</h1>

  <?php settings_errors(); ?>

  <?php if (class_exists('\Inc\Base\Methods')) : ?>
  <div class="payment-status">
    <h4>Status Przelewy24</h4>
    <?php $status =  Methods::GetP24Status();
      echo time();
      if (isset($status->data)) {
        echo "<div class='payment-status__btn payment-status__btn--active'>Aktywny</div>";
      } else {
        echo "<div class='payment-status__btn payment-status__btn--inactive' >Płatność niedostępna</div>";
      }
      ?>
  </div>
  <?php endif; ?>

  <form method="post" action="options.php" class="p24__form">
    <?php
    settings_fields('gtw_przelewy24_plugin_settings');
    do_settings_sections('gtwprzelewy24');
    submit_button();
    ?>
  </form>
</div>
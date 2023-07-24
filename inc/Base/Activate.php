<?php

namespace Inc\Base;

class Activate
{
  public static function activate()
  {
    flush_rewrite_rules();

    global $wpdb;

    if (!get_option('gtw_p24_version')) {
      update_option('gtw_p24_version', '1.0.0');
      update_option('gtw_p24_api', '1.0.16');

      $table_name = $wpdb->prefix . "gtw_p24_orders";
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE $table_name (
        order_id bigint(9) NOT NULL AUTO_INCREMENT,
        session_id char(16) NOT NULL,
        post_id bigint(9) NOT NULL,
        client_id bigint(9) NOT NULL,
        order_time datetime,
        payment_posted datetime,
        payment_return boolean DEFAULT 0,
        payment_return_time datetime,
        client_email tinytext NOT NULL,
        payment_method smallint,
        payment_order_id bigint(9),
        order_price tinytext NOT NULL,
        order_status varchar(25) NOT NULL,
        PRIMARY KEY  (order_id)
      ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      dbDelta($sql);
    }

    if ($wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'payments'") === null) {
      $current_user = wp_get_current_user();

      $page = [
        'post_title' => 'payments',
        'post_name' => 'payments',
        'post_status' => 'publish',
        'post_author' => $current_user->ID,
        'post_type' => 'page',
        'post_content' => '<!-- wp:shortcode --> [gtw_p24_payments]<!--> /wp:shortcode -->'
      ];
      wp_insert_post([$page]);
    }
  }
}
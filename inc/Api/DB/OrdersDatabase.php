<?php

namespace Inc\Api\DB;

use Inc\Base\Methods;

class OrdersDatabase
{

  public function register()
  {
    add_action('init', [$this, 'registerMetadataTable']);
    add_action('delete_post', [$this, 'delete_post']);
    // $this->getAllOrders();
  }

  public function insertSingleOrder($order_time = null, $session_id = null, $client_id = null, $post_id = null, $client_email = null, $order_price = null, $order_status = null)
  {
    global $wpdb;

    $wpdb->insert(
      $wpdb->gtw_p24_orders,
      [
        "order_id" => 0,
        "order_time" => $order_time,
        "session_id" => $session_id,
        "client_id" => $client_id,
        "post_id" => $post_id,
        "client_email" => $client_email,
        "order_price" => $order_price,
        "order_status" => $order_status
      ],
      [
        '%d', '%s', '%s', '%d', '%d', '%s',
        '%s', '%s'
      ]
    );
  }

  public function updateSingleOrder($post_id)
  {
    global $wpdb;

    $wpdb->update(
      $wpdb->gtw_p24_orders,
      [
        "order_status" => 'zapłacone'
      ],
      [
        "post_id" => $post_id,
      ],
      [],
      ['%d']
    );
  }

  public function deleteSingleOrder($post_id)
  {
    global $wpdb;

    $wpdb->delete(
      $wpdb->gtw_p24_orders,
      [
        'post_id' => $post_id
      ],
      ['%d']
    );
  }

  public static function getCurrentSessionOrder($current_id = null)
  {

    if ($current_id !== null && is_int($current_id)) {
      global $wpdb;

      $query = $wpdb->prepare(
        "SELECT * FROM $wpdb->gtw_p24_orders WHERE post_id = %d",
        $current_id
      );

      $results = $wpdb->get_row($query, ARRAY_A);


      if (!empty($results)) {
        return $results;
      }
    }

    return null;
  }

  public static function checkIfSessionIdExists($session_id = null)
  {
    if ($session_id !== null) {
      global $wpdb;

      $query = $wpdb->prepare(
        "SELECT * FROM $wpdb->gtw_p24_orders WHERE session_id = %s",
        $session_id
      );

      $results = $wpdb->get_results($query, ARRAY_A);
      $count = $wpdb->num_rows;


      if (!empty($results)) {
        return $count;
      } else {
        return 0;
      }
    }
  }


  public function getAllOrders()
  {
    global $wpdb;

    $query =  "SELECT * FROM wp_gtw_p24_orders";

    $results = $wpdb->get_results($query, ARRAY_A);

    if (!empty($results)) {
      return $results;
    }

    return null;
  }

  public function registerMetadataTable()
  {
    global $wpdb;
    $wpdb->gtw_p24_orders = $wpdb->prefix . 'gtw_p24_orders';
  }

  public function delete_post($post_id)
  {
    if (!current_user_can('delete_posts')) {
      return;
    }

    global $wpdb;

    $wpdb->delete(
      $wpdb->gtw_p24_orders,
      [
        'post_id' => $post_id
      ],
      ['%d']
    );
  }
}

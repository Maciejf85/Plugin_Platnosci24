<?php

use Inc\Api\DB\OrdersDatabase;
?>
<div class="wrap">
  <h1 class="wp-heading-inline">Zamówienia</h1>
  <div></div>
  <?php

  if (class_exists('Inc\Api\DB\OrdersDatabase')) {
    $db = new OrdersDatabase();
    $db_orders = $db->getAllOrders();

    if (!empty($db_orders)) {

      echo "<table class='order-table'>";
      echo "<tr class='order-table__caption'>";
      echo "<td>#</td>";
      echo "<td>Nazwa sesji</td>";
      echo "<td>Klient</td>";
      echo "<td>Data zamówienia</td>";
      echo "<td>Metoda płatności</td>";
      echo "<td>Data płatności</td>";
      echo "<td>Data sesji</td>";
      echo "<td>E-mail </td>";
      echo "<td>Cena</td>";
      echo "<td>Status</td>";
      echo "</tr>";
      if (!empty($db_orders) && is_array($db_orders)) {
        foreach ($db_orders as $single_order) {
          echo "<tr>";
          if (!empty($single_order) && is_array($single_order)) {
            foreach ($single_order as $key => $value) {
              switch ($key) {
                case "order_price":
                  echo "<td>" . $value / 100 . " PLN" .  " </td>";
                  break;
                case "post_id":
                  echo "<td>" . get_the_title($value) .  " </td>";
                  echo "<td>" . get_the_date('d-m-Y H:i', $value) .  " </td>";
                  break;
                case "client_id":
                  echo "<td>" . get_the_title($value) .  " </td>";
                  break;
                case "session_id":
                  break;
                default:
                  echo "<td>" . $value .  " </td>";
                  break;
              }
            }
          }
          echo "</tr>";
        }
      }
      echo "</table>";
    } else {
      echo "<h3>Brak zamówień</h3>";
    }
  }
  ?>
</div>
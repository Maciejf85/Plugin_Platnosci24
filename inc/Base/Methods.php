<?php

namespace Inc\Base;

use \Datetime;
use \DateTimeZone;
use Inc\Api\DB\OrdersDatabase;


class Methods
{
  public static function GetCurrentTime($format = "Y-m-d H:i:s", $timezone = "Europe/Warsaw")
  {
    $date = new DateTime();
    $date->setTimezone(new DateTimeZone($timezone));
    return $date->format($format);
  }

  public static function CreateSessionId($length = 16)
  {
    $unique = false;
    $p24_session_id = null;

    while (!$unique) {
      $bytes = random_bytes($length);
      $p24_session_id = bin2hex($bytes);

      if (OrdersDatabase::checkIfSessionIdExists($p24_session_id) > 0) {
        continue;
      } else {
        $unique = true;
      }
    }

    return $p24_session_id;
  }

  public static function GetP24Status()
  {

    $type = get_option('gtw_p24Mode');
    $username = get_option('gtw_p24ID');
    $password = get_option('gtw_p24Raports');
    $url = 'https://' . $type . '.przelewy24.pl/api/v1/testAccess';

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        "Content-type: application/json",
        "Authorization: Basic " . base64_encode("$username:$password")
      ]
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);
    $result = json_decode($response);
    return $result;

    curl_close($curl);
  }
}

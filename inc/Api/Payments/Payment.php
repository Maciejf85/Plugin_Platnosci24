<?php

namespace Inc\Api\Payments;

use Inc\Base\Methods;
use Inc\Api\DB\OrdersDatabase;

class Payment
{
  private $PRZELEWY24_MERCHANT_ID = "";
  private $PRZELEWY24_CRC = "";
  private $PRZELEWY24_RAPORTS = "";
  private $PRZELEWY24_TYPE = "";
  private $ENCODING = "UTF-8";
  private $LANGUAGE = "pl";
  private $COUNTRY = "PL";
  private $CURRENCY = "PLN";

  private $CURRENT_CLIENT_ID = 0;
  private $CURRENT_POST_ID = 0;


  private $PAYMENT_AMOUNT = 0;
  private $PAYMENT_DESCRIPTION = "";
  private $CURRENT_URL = "";
  private $CURRENT_URL_STATUS = GTW_P24_SITE_URL . "/order/";
  private $CLIENT_NAME = "";
  private $CLIENT_EMAIL = "";



  public function register()
  {
    add_action('init', [$this, 'getToken'], 10, 2);
  }

  public function __construct()
  {
    $this->getBaseData();
  }

  public function getBaseData()
  {
    $this->PRZELEWY24_MERCHANT_ID = get_option('gtw_p24ID');
    $this->PRZELEWY24_CRC = get_option('gtw_p24CRC');
    $this->PRZELEWY24_RAPORTS = get_option('gtw_p24Raports');
    $this->PRZELEWY24_TYPE = get_option('gtw_p24Mode');
  }

  // START PAYMENT PROCESS
  // -----------------------------------------
  public function getToken()
  {

    if (isset($_POST['send_payment'])) {
      // ob_start();

      if (!empty(($_POST['p24_amount']))) {
        $amount = $_POST['p24_amount'];
        $this->PAYMENT_AMOUNT = filter_var($amount, FILTER_SANITIZE_NUMBER_INT);
      } else {
        exit;
      }

      if (!empty(($_POST['gtw_current_post_id']))) {
        $this->CURRENT_POST_ID = intval(filter_var($_POST['gtw_current_post_id'], FILTER_SANITIZE_NUMBER_INT));
        if (empty(get_the_title($this->CURRENT_POST_ID))) {
          exit;
        } else {
          $this->PAYMENT_DESCRIPTION = "Zapłata za sesję: " . get_the_title($this->CURRENT_POST_ID);
        }
      } else {
        exit;
      }

      if (!empty(($_POST['gtw_current_client_id']))) {
        $this->CURRENT_CLIENT_ID = intval(filter_var($_POST['gtw_current_client_id'], FILTER_SANITIZE_NUMBER_INT));
        if (empty(get_the_title($this->CURRENT_CLIENT_ID))) {
          exit;
        } else {
          $this->CLIENT_NAME = get_the_title($this->CURRENT_CLIENT_ID);
          $client_data = get_field("client_data", $this->CURRENT_CLIENT_ID);


          if (!empty($client_data['client_email'])) {
            $this->CLIENT_EMAIL = $client_data['client_email'];
          } else {
            exit;
          }
        }
      } else {
        exit;
      }

      if (!empty($_POST['gtw_current_url'])) {
        $this->CURRENT_URL = filter_var($_POST['gtw_current_url'], FILTER_SANITIZE_URL);
      } else {
        exit;
      }

      if (!empty(OrdersDatabase::getCurrentSessionOrder(0))) {
        exit;
      }

      // Adres dla weryfikacji płatności
      $redirect = $this->Pay();
      Header('Location: ' . $redirect);
      exit;
    }
  }


  // create token and redirect to payment page
  // -----------------------------------------
  public function Pay()
  {
    $token = $this->CreateToken();
    if ($token !== 0) {
      return 'https://' . $this->PRZELEWY24_TYPE . '.przelewy24.pl/trnRequest/' . $token;
    }
  }

  // CREATE TOKEN
  // -----------------------------------------
  public function CreateToken()
  {

    $url = 'https://' . $this->PRZELEWY24_TYPE . '.przelewy24.pl/api/v1/transaction/register';
    $username = $this->PRZELEWY24_MERCHANT_ID;
    $password = $this->PRZELEWY24_RAPORTS;
    $sessionId = Methods::CreateSessionId();
    $session_status = "oczekuje";

    $sign_format = json_encode([
      "sessionId" => $sessionId,
      "merchantId" => intval($this->PRZELEWY24_MERCHANT_ID),
      "amount" => intval($this->PAYMENT_AMOUNT),
      "currency" => strval($this->CURRENCY),
      "crc" => strval($this->PRZELEWY24_CRC)
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $current_hash = hash("sha384", $sign_format);

    $data = array(
      'merchantId' => $this->PRZELEWY24_MERCHANT_ID,
      'posId' => $this->PRZELEWY24_MERCHANT_ID,
      'sessionId' => $sessionId,
      'amount' => $this->PAYMENT_AMOUNT,
      'currency' => $this->CURRENCY,
      'description' => $this->PAYMENT_DESCRIPTION,
      'email' => $this->CLIENT_EMAIL,
      'client' => $this->CLIENT_NAME,
      'country' => $this->COUNTRY,
      'language' => $this->LANGUAGE,
      'urlReturn' => $this->CURRENT_URL,
      'urlStatus' => $this->CURRENT_URL_STATUS,
      'sign' => $current_hash,
      'encoding' => $this->ENCODING
    );

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => [
        "Content-type: application/json",
        "Authorization: Basic " . base64_encode("$username:$password")
      ]
    ];


    $curl = curl_init();
    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);
    $result = json_decode($response);

    curl_close($curl);

    if (isset($result->data)) {
      $token = $result->data->token;
      $time = Methods::GetCurrentTime();
      $db = new OrdersDatabase;

      $db->insertSingleOrder($time, $sessionId, $this->CURRENT_CLIENT_ID, $this->CURRENT_POST_ID, $this->CLIENT_EMAIL, $this->PAYMENT_AMOUNT, $session_status, $this->CURRENCY);

      return $token;
    } else {
      return 0;
    }
  }

  // VERIFY TRANSACTION
  // -----------------------------------------
  public function Verify($data = null)
  {
    $sign_data = json_encode([
      "sessionId" => strval($data['sessionId']),
      'orderId' => intval($data['orderId']),
      "amount" => intval($data['amount']),
      "currency" => strval("PLN"),
      "crc" => strval($this->PRZELEWY24_CRC)
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


    $current_hash = hash("sha384", $sign_data);

    $username = $this->PRZELEWY24_MERCHANT_ID;
    $password = $this->PRZELEWY24_RAPORTS;

    $curr_data = array(
      'merchantId' => $data['merchantId'],
      'posId' =>  $data['merchantId'],
      'sessionId' => $data['sessionId'],
      'amount' => $data['amount'],
      'currency' => "PLN",
      'orderId' => $data['orderId'],
      'sign' => $current_hash,
    );


    $options = [
      CURLOPT_URL => 'https://' . $this->PRZELEWY24_TYPE . '.przelewy24.pl/api/v1/transaction/verify',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => json_encode($curr_data),
      CURLOPT_HTTPHEADER => [
        "Content-type: application/json",
        "Authorization: Basic " . base64_encode("$username:$password")
      ]
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);
    $result = json_decode($response);

    curl_close($curl);

    if (isset($result->data)) {
      if ($result->data->status === "success") {
        $time = Methods::GetCurrentTime();
        $db = new OrdersDatabase;
        $db->updateVerificationOrder($data['sessionId'], $time, "zapłacone", $data['methodId'], $data['orderId'], null);
      }
    } elseif (isset($result->error)) {
      $time = Methods::GetCurrentTime();
      $db = new OrdersDatabase;
      $db->updateVerificationOrder($data['sessionId'], $time, null, null, null, $result->error);
    }
  }
}

<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;

class BLSMS extends Controller
{
    static function _sendMessageBLSM($message, $recipient)
    {
        $recipient = validRecipient($recipient);
        $api_key = env('SMS_API_KEY');
        $secret_key = env('SMS_API_SECRET');
        $postData = array(
            'source_addr' => env('SMS_SENDER'),
            'encoding' => 0,
            'schedule_time' => '',
            'message' => $message,
            'recipients' => [
                array('recipient_id' => '1', 'dest_addr' => $recipient)
            ]
        );
        $Url = 'https://apisms.beem.africa/v1/send';
        $ch = curl_init($Url);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode("$api_key:$secret_key"),
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));
        $response = curl_exec($ch);
        if ($response === FALSE) {
            die(curl_error($ch));
        }
        $response = json_decode($response);
        return $response;
    }
}

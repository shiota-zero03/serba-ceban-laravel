<?php

namespace App\Services;

class WhatsappService {

    public function __construct(Type $var = null) {
        $this->curlLink = env('WHATSAPP_LINKCURL');
        $this->appKey = env('WHATSAPP_APPKEY');
        $this->authKey = env('WHATSAPP_AUTHKEY');
    }
    public function sendMessage( string $message, string $phone )
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->curlLink,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'appkey' => $this->appKey,
                'authkey' => $this->authKey,
                'to' => $phone,
                'message' => $message,
                'sandbox' => 'false'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
    public function sendMessageWithFile( string $message, string $phone, string $file )
    {
        $curl = curl_init();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->curlLink,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'appkey' => $this->appKey,
                'authkey' => $this->authKey,
                'to' => $phone,
                'message' => $message,
                'file' => $file,
                'sandbox' => 'false'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}

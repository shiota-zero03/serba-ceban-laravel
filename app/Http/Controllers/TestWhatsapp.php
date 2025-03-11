<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestWhatsapp extends Controller
{
    public function index(){
        $curl = curl_init();

$message = '
Kepada Yth. Mitra UMKM \
Berikut informasi mengenai request produk untuk dikirimkan. \
- Nama produk : harga \\

Tertanda  \
Admin Serba Ceban
';

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.wapanels.com/api/create-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'appkey' => '2240a699-e56e-4e98-8423-12a571db9d2a',
                'authkey' => 'YXUFjEr3CGpjgKRbdzADb2a6vst1EKyyHEMRb7n7QoqyWVZQCu',
                'to' => '6282275713049',
                'message' => $message,
                'sandbox' => 'false'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
}

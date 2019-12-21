<?php
$url = 'https://fcm.googleapis.com/v1/projects/prakuliah-faaa8/messages:send';
$fields = array(
    'message' => array(
        'token' => 'ea6AwEGR1fc:APA91bGnyLt2ywlN8aaPafRlPnkVtB2CuZ3voDBCXXJoh0n2nLr1xTh1uirU_mKogHfLjoVlPPwKEK6TIWsfXEN_tZ_4l0ZwSHB__GNtnVPxIINmQpJySwS740IdH36q5hc3B0DMyyF3',
        'data' => array(
            "message" => "Hello world"
        ),
        'notification' => array(
            'title' => 'Ini adalah judul',
            'body' => 'Ini adalah isi pesan'
        )
    )
);
$fields = json_encode($fields);
$headers = array(
    'Authorization: Bearer ya29.c.Kl61B5rAsnt7AkCgasJhmi88Sn3cexLn9Vg0RetgJL6QgLPV2pAqNBwZspbYcM-gM2PycqzqWJGsHN5p3Rq98CoQHQP9qRMS-ZLAQ1tObJHJCO0QC4_HYfxDP3KRkMAC',
    'Content-Type: application/json'
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
$result = curl_exec($ch);
echo $result;
curl_close($ch);

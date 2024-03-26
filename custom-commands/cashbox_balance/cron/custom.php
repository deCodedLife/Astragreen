<?php

$curl = curl_init();

curl_setopt( $curl, CURLOPT_URL, "https://dev.docacrm.com" );
curl_setopt( $curl, CURLOPT_POST, true );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $curl, CURLOPT_POSTFIELDS, [
    "object" => "cashbox_balance",
    "command" => "update"
] );

$response_data = curl_exec( $curl );
curl_close( $curl );

exit();
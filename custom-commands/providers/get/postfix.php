<?php

$purchasesReturn = [];

foreach ( $response[ "data" ] as $row ) {

    $purchases = $API->DB->from( "purchases" )
        ->where( "provider_id", $row[ "id" ] );

    foreach ( $purchases as $purchases ) {

        $row[ "totalPurchases" ] += $purchases[ "price" ];

    }

    $purchasesReturn[] = $row;

}

$response[ "data" ] = $purchasesReturn;
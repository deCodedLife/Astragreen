<?php
$purchaseDetail = $API->DB->from( "purchases" )
    ->where( "id", $requestData->id )
    ->limit( 1 )
    ->fetch();

$purchases_products = $API->DB->from( "purchases_products" )
    ->where( "row_id", $requestData->id );

foreach ( $purchases_products as $purchase_product ){

    $productActive = $API->DB->from( "warehouses" )
        ->where( [
            "product_id" => $purchase_product[ "product_id" ],
            "store_id" => $purchaseDetail[ "store_id" ]
        ] )
        ->limit( 1 )
        ->fetch();

    $API->DB->update( "warehouses" )
        ->set([
            "count" =>  (int)$productActive[ "count" ] - (int)$purchase_product[ "count" ]
        ])
        ->where( [
            "product_id" => (int)$purchase_product[ "product_id" ],
            "store_id" => (int)$purchaseDetail[ "store_id" ]
        ] )
        ->execute();

}


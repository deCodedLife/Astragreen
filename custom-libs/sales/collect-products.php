<?php

$products = array_map(
    fn ( $element ) => $element->id ?? 0,
    (array) $requestData->astragreen_products
);

if ( empty( $products ) ) $products = [0];

$productsDetails = $API->DB->from( "products" )
    ->where( "id", $products )
    ->fetchAll( "id" );

foreach ( $requestData->astragreen_products as $product )
    $productsPrice += $productsDetails[ $product->id ][ "price" ] * $product->amount ?? 0;

$products = $productsDetails;
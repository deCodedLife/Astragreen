<?php

$products = $API->DB->from( "products" )
    ->innerJoin( "purchases_products on purchases_products.product_id = products.id" )
    ->innerJoin( "purchases on purchases.id = purchases_products.row_id" )
    ->innerJoin( "manufacturers on manufacturers.id = products.manufacturer_id" )
    ->select( null )
    ->select( [
        "products.title",
        "products.manufacturer_id",
        "manufacturers.title as manufacturersTitle ",
        "purchases.id",
        "purchases.weekday",
        "purchases.provider_id",
        "purchases.store_id",
        "purchases.created_at",
        "purchases_products.product_id",
        "purchases_products.count",
        "purchases_products.price",
    ] )
    ->where( [
        "products.is_active" => "Y",
        "purchases.status" => "active"
    ] );

$catalog = [];

if ( !empty( $products ) ) {

    foreach ( $products as $product ) {

        $catalog[] = [

            "title" => $product[ "title" ],
            "price" => $product[ "price" ],
            "count" => $product[ "count" ],
            "manufacturer_id" => $product[ "manufacturer_id" ],

        ];

    }

}

$warehouses = $API->DB->from( "warehouses" )
    ->innerJoin( "manufacturers on manufacturers.id = warehouses.manufacturer_id" )
    ->select( null )
    ->select( [
        "warehouses.title",
        "warehouses.price",
        "warehouses.count",
        "warehouses.manufacturer_id",
        "manufacturers.title as manufacturersTitle "
    ] )
    ->where( [
        "warehouses.is_active" => "Y"
    ] );

foreach ( $warehouses as $warehouse ) {

    $catalog[] = [

        "title" => $warehouse[ "title" ],
        "price" => $warehouse[ "price" ],
        "count" => $warehouse[ "count" ],
        "manufacturer_id" => $warehouse[ "manufacturer_id" ],

    ];

}

foreach ( $catalog as $item ) {

    $API->DB->insertInto( "catalog" )
        ->values( [
            "title" => $item[ "title" ],
            "price" => $item[ "price" ],
            "count" => $item[ "count" ],
            "is_active" => "Y",
            "manufacturer_id" => $item[ "manufacturer_id" ],
        ] )
        ->execute();

}



<?php

/**
 * @file
 * Хуки на Закупку
 */
$formFieldsUpdate = [];

/**
 * Расчет стоимости закупки
 */

$purchaseCost = 0;
$purchaseSale = 0;

if ( $requestData->purchases_products ) {

    foreach ( $requestData->purchases_products as $productKey => $product ) {

        $purchaseCost += $product->cost * $product->count;
        $purchaseSale += $product->discount;

        $product->sum = $product->cost * $product->count - $product->discount;

        if ( $product->price && $product->cost ) {

            $product->margin = (( $product->price - $product->cost ) / $product->price ) * 100;

        }

        $formFieldsUpdate[ "purchases_products" ][ "value" ][ $productKey ] = $product;

    } // foreach. $requestData->purchases_products

} // if. $requestData->purchases_products

if ( $requestData->deliveryPrice ) {

    $purchaseCost += $requestData->deliveryPrice;

} // if. $requestData->deliveryPrice

$formFieldsUpdate[ "price" ][ "value" ] = $purchaseCost;

if ( $requestData->saleType == "fixed" ) {

    $formFieldsUpdate[ "saleSize" ][ "value" ] = $purchaseSale;

} else if ( $requestData->saleType == "percent" ) {

    $formFieldsUpdate[ "saleSize" ][ "value" ] = ( $purchaseSale / $purchaseCost ) * 100 ;

}
if ( $requestData->created_at ) $formFieldsUpdate[ "weekday" ][ "value" ] = date("l", strtotime($requestData->created_at));



$API->returnResponse( $formFieldsUpdate );

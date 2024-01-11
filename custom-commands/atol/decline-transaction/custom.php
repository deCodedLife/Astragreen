<?php


/**
 * Изменение статуса оплаты
 */

$saleDetails = $API->DB->from( "salesList" )
    ->where( "id", $requestData->sale_id )
    ->fetch();

if ( !$saleDetails ) $API->returnResponse();
if ( $saleDetails[ "status" ] === "done" ) $API->returnResponse();

$API->DB->update( "salesList" )
    ->set( [
        "status" => $requestData->status ?? "error",
        "error" => $requestData->description ?? ""
    ] )
    ->where( "id", $requestData->sale_id )
    ->execute();

<?php

ini_set( "display_errors", true );

if ( $requestData->is_combined == 'N' && $requestData->object ) {

    $requestData->visits_ids = [
        $requestData->object => [ $requestData->id ]
    ];

}

$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
require_once $publicAppPath . '/custom-libs/sales/business_logic.php' ;
require_once "update-products.php";
require_once "sum-fields-update.php";

$API->returnResponse( $formFieldsUpdate );
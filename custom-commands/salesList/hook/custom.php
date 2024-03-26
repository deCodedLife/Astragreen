<?php
ini_set( "display_errors", true );

/**
 * Обновление полей
 */
$formFieldsUpdate = $formFieldsUpdate ?? [];
$isReturn = ( $requestData->action ?? 'sell' ) === "sellReturn";

$amountOfPhysicalPayments = $amountOfPhysicalPayments ?? (
    $requestData->summary - ( $requestData->sum_bonus + $requestData->sum_deposit ) );
require_once "sum-fields-update.php";


if ( $isReturn ) {

    $formFieldsUpdate[ "sum_deposit" ][ "is_visible" ] = false;
    $formFieldsUpdate[ "sum_bonus" ][ "is_visible" ] = false;

} // if ( isReturn )


$API->returnResponse( $formFieldsUpdate );
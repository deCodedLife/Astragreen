<?php


/**
 * Получение детальной информации о пользователе
 */

$userDetails = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();

if ( !$userDetails[ "store_id" ] ) $userDetails[ "store_id" ] = $API->DB->from( "stores" )->limit( 1 )->fetch()["id"];

if ( $requestData->pay_method == "legalEntity" ) {

    $legalEntityDetails = $API->DB->from( "legal_entities" )
        ->innerJoin( "legal_entity_clients ON legal_entity_clients.legal_entity_id = legal_entities.id" )
        ->where( "legal_entity_clients.client_id", $requestData->client_id )
        ->limit(1)
        ->fetch();

    $API->DB->update( "legal_entities" )
        ->set( "balance", $legalEntityDetails[ "balance" ] - $requestData->summary )
        ->where( "id", $legalEntityDetails[ "id" ] )
        ->execute();

}


/**
 *  Создание транзакции
 */
$saleID = $API->DB->insertInto( "salesList" )
    ->values( [
        "employee_id" => (int) $API::$userDetail->id,
        "action" => $requestData->action,
        "sum_bonus" => $requestData->sum_bonus ?? 0,
        "sum_deposit" => $requestData->sum_deposit ?? 0,
        "sum_card" => $requestData->sum_card,
        "sum_cash" => $requestData->sum_cash,
        "status" => $requestData->pay_method == "legalEntity" ? "done" : "waiting",
        "store_id" => (int) $userDetails[ "store_id" ],
        "pay_method" => $requestData->pay_method,
        "online_receipt" => $requestData->online_receipt,
        "summary" => $requestData->summary,
        "client_id" => $requestData->client_id,
    ] )
    ->execute();



/**
 *  Заполнение посещений транзакции
 */

foreach ( $requestData->visits_ids ?? [] as $key => $visits ) {

    foreach ( $visits as $visit ) {

        $table = ( $key ?? "visits" ) === "equipmentVisits" ? "salesEquipmentVisits" : "saleVisits";

        $API->DB->insertInto( $table )
            ->values( [
                "sale_id" => $saleID,
                "visit_id" => $visit
            ] )
            ->execute();

        if ( $requestData->pay_method != "legalEntity" ) continue;

        $API->DB->update( "visits" )
            ->set( [
                "visits.is_payed" => 'Y'
            ] )
            ->where( "id", $visit )
            ->execute();

    }


} // foreach. $requestData->visits_ids as $visit


/**
 * Добавление продуктов к продаже
 */
foreach ( $requestData->products as $product ) {

    $product = (array) $product;
    $product[ "sale_id" ] = $saleID;

    if ( !$product[ "product_id" ] ) continue;

    $API->DB->insertInto( "salesProductsList" )
        ->values( $product )
        ->execute();

} // foreach. $requestData->pay_object as $service



$API->addEvent( "schedule" );
$API->addEvent( "salesList" );
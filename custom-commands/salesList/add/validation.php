<?php


/**
 * Получение детальной информации о клиенте
 */

if ( !isset( $requestData->employee_id ) ) $API->returnResponse( "Ошибка. Не указан сотрудник", 500 );
if ( !isset( $requestData->products ) ) $API->returnResponse( "Ошибка. Продукты не подгрузились", 500 );

if ( $requestData->pay_method != "parts" ) {

    if ( $requestData->sum_cash != 0 && $requestData->pay_method != "cash" )
        $API->returnResponse( "Ошибка. Несовпадение типа и значений оплаты", 500 );

    if ( $requestData->sum_card != 0 && $requestData->pay_method != "card" )
        $API->returnResponse( "Ошибка. Несовпадение типа и значений оплаты", 500 );

}

foreach ( $requestData->products as $product ) {

    if (
        !isset( $product->title ) ||
        !isset( $product->product_id )
    ) $API->returnResponse( "Ошибка. Продукты сформированы некорректно", 500 );

}

$clientDetails = $API->DB->from( "clients" )
    ->where( "id", $requestData->client_id )
    ->fetch();

$clientEntity = $API->DB->from( "legal_entity_clients" )
    ->where( "client_id", $requestData->client_id )
    ->fetch();

$entityDetails = $API->DB->from( "legal_entities" )
    ->where( "id", $clientEntity[ "legal_entity_id" ] )
    ->fetch();

if ( $requestData->sum_entity > $entityDetails[ "balance" ] )
    $API->returnResponse(  "Недостаточно средств на счёте юридического лица", 400 );

if ( $requestData->sum_deposit > $clientDetails[ "deposit" ] )
    $API->returnResponse( "Недостаточно средств на депозитном счёте клиента", 400 );

if ( $requestData->sum_bonus > $clientDetails[ "bonuses" ] )
    $API->returnResponse( "Недостаточно средств на бонусном счёте клиента", 400 );


/**
 * Проверка на день оплаты.
 * Оплата совершается только день в день
 */
foreach ( $requestData->visits_ids ?? [] as $key => $visits ) {

    if ( empty( $visits ) ) continue;

    $visitDetails = $API->DB->from( $key )
        ->where( "id", $visits[0] ?? 0 )
        ->fetch();


    $visitDate = explode( " ", $visitDetails[ "start_at" ] );

    if ( $visitDate[ 0 ] != date( "Y-m-d" ) )
        $API->returnResponse( "Оплаты совершаются только день в день,{$visitDate[0]}", 500 );

} // foreach ( $requestData->visits_ids ?? [] as $visit )



/**
 * Проверка корректности суммы оплаты с суммой посещения
 */

$saleSummary = 0;
$paymentsSummary =
    $requestData->sum_deposit +
    $requestData->sum_bonus +
    $requestData->sum_card +
    $requestData->sum_cash +
    $requestData->sum_entity;



/**
 * Валидация итоговой суммы
 */

if ( $paymentsSummary != $requestData->summary )
    $API->returnResponse( "Сумма всех способов оплаты не совпадает с итоговой суммой посещения", 400 );

/**
 * Проверка корректности введённых значений
 */

$userDetail = $API->DB->from( "users_stores" )
    ->innerJoin( "users on users.id = users_stores.user_id" )
    ->where( "users.id", $API::$userDetail->id );

foreach ( $userDetail as $item ) {

    $storeID = $item[ "store_id" ];
    break;

}

if ( ( $storeID ?? -1 ) != $requestData->store_id ) $API->returnResponse( "Ошибка. Филиал не корректный", 500 );
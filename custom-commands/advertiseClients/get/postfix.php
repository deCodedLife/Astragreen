<?php
/**
 * @file
 * Список "ректамные источники
 */

/**
 * Сформированный список
 */
$returnAdvertises = [];


/**
 * Фильтр источников
 */
$advertisesFilter = [];

/**
 * Фильтр посещений
 */
$visitsFilter = [];

/**
 * Формирование фильтров
 */
if ( $requestData->id ) $advertisesFilter[ "id" ] = $requestData->id;

if ( $requestData->store_id ) $visitsFilter[ "visits.store_id" ] = $requestData->store_id;
if ( $requestData->start_at ) $visitsFilter[ "end_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $visitsFilter[ "end_at <= ?" ] = $requestData->end_at . " 23:59:59";
$visitsFilter[ "visits.is_payed" ] = "Y";

/**
 * Получение рекламных источников
 */
$advertises = $API->DB->from( "advertise" )
    ->where( $advertisesFilter );

/**
 * Обход рекламных источников
 */
foreach ( $advertises as $advertise ) {

    /**
     * Колличество посещений
     */
    $visitsCount = 0;

    /**
     * Колличество посещений
     */
    $visitsPrice = 0;

    /**
     * Получение клиентов
     */
    $clients = $API->DB->from( "clients" )
        ->where( "advertise_id", $advertise [ "id" ] );

    foreach ( $clients as $client ) {

        /**
         * Фильтрация посещений по клиенту
         */
        $visitsFilter[ "visits_clients.client_id" ] = $client[ "id" ];

        /**
         * Получение посещений Клиента
         */
        $clientVisits = $API->DB->from( "visits" )
            ->leftJoin( "visits_clients ON visits_clients.visit_id = visits.id" )
            ->select( null )->select( [ "visits.id", "visits.start_at", "visits.store_id", "visits.price", "visits.status", "visits.is_payed" ] )
            ->where( $visitsFilter )
            ->orderBy( "visits.start_at desc" )
            ->limit( 0 );

        /**
         * Обход посещений клиента
         */
        foreach ( $clientVisits as $clientVisit ) {

            $visitsCount++;
            $visitsPrice += $clientVisit[ "price" ];


        } // foreach. $userVisits

    } // foreach. $clients

    $returnAdvertises[] = [

        "id" => $advertise["id"],
        "title" => $advertise["title"],
        "clientsCount" => count( $clients ),
        "visitsCount" => $visitsCount,
        "price" => $visitsPrice

    ];

}

$response[ "data" ] = $returnAdvertises;

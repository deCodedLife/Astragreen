<?php


/**
 * Фильтр по Филиалу
 */

if ( $requestData->stores_id ) {

    /**
     * Отфильтрованные Записи
     */
    $filteredRows = [];


    /**
     * Фильтрация Записей
     */

    foreach ( $response[ "data" ] as $row ) {

        $isContinue = true;


        /**
         * Запрос специальностей
         */

        $userStores = $API->DB->from( "users_stores" )
            ->where( "user_id", $row[ "id" ] );

        foreach ( $userStores as $userStore )
            if ( $userStore[ "store_id" ] == $requestData->stores_id ) $isContinue = false;

        if ( $isContinue ) continue;


        $filteredRows[] = $row;

    } // foreach. $response[ "data" ]


    /**
     * Обновление списка Записей
     */
    $response[ "data" ] = $filteredRows;

} // if. $requestData->store_id

/**
 * Подстановка ФИО
 */

$returnRows = [];

foreach ( $response[ "data" ] as $row ) {

    $row[ "fio" ] = $row[ "last_name" ] . " " . $row[ "first_name" ] . " " . $row[ "patronymic" ];
    $returnRows[] = $row;

} // foreach. $response[ "data" ]

$response[ "data" ] = $returnRows;
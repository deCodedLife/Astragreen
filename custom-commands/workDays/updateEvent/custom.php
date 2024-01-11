<?php

/**
 * Вызываем метод создания ячеек и их валидации
 */
require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/workdays/createEvents.php";
require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/workdays/validate.php";


/**
 * Если мы находимся тут, то никаких накладок не выявлено
 */

/**
 * Вытаскиваем список дней для последующего добавления
 * в связанную таблицу workDaysWeekdays
 */
$workDays = (array)$requestData->work_days;
unset( $requestData->work_days );
unset( $requestData->id );


/**
 * Изменяем правило из запроса, попутно сохраняя ID
 * создаваемого объекта
 */
$ruleID = $API->DB->update( "workDays" )
    ->set( (array) $requestData )
    ->where( "id", $ruleDetails[ "id" ] )
    ->execute();


/**
 * Очистка и запись данных в связанную таблицу
 */
$API->DB->deleteFrom( "workDaysWeekdays" )
    ->where( "rule_id", $ruleDetails[ "id" ] )
    ->execute();

foreach ( $workDays as $workDay ) {

    $API->DB->insertInto( "workDaysWeekdays" )
        ->values([
            "rule_id" => $ruleDetails[ "id" ],
            "workday" => $workDay
        ])
        ->execute( );

}

if ( $requestData->is_weekend ) {

    $API->DB->deleteFrom( "scheduleEvents" )
        ->where( [
            "user_id" => $requestData->user_id,
            "event_from > ?" => $begin->format( "Y-m-d 00:00:00" ),
            "event_to < ?" => $begin->format( "Y-m-d 23:59:59" ),
            "store_id" => $requestData->store_id
        ] )
        ->execute();

}

foreach ( $newSchedule as $scheduleEvent ) {

    unset( $scheduleEvent[ "id" ] );

    $API->DB->insertInto( "scheduleEvents" )
        ->values( $scheduleEvent )
        ->execute();

}

/**
 * Блокируем создание записей
 */
$API->returnResponse();
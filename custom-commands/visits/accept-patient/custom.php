<?php

/**
 * @file
 * Вызов клиента
 */


$visitDetail = $API->DB->from( "visits" )
    ->where( "id", $requestData->id)
    ->limit( 1 )
    ->fetch();

$visits = $API->DB->from( "visits" )
    ->where(
        [
            "user_id" => $visitDetail[ "user_id" ],
            "start_at >= ?" => date("Y-m-d") . " 00:00:00",
            "start_at <= ?" => date("Y-m-d") . " 23:59:59",
            "status" => "process",
        ]
    );

$solo = true;

foreach ( $visits as $visit ) {

    if ( $visit[ "id" ] != $requestData->id ) {

        $solo = false;
        break;

    }

}


if ( $solo ) {

    $API->DB->update( "visits" )
        ->set( [
            "status" => "process"
        ] )
        ->where( [
            "id" => $requestData->id
        ] )
        ->execute();

} else {

    $API->returnResponse( "Завершите предыдущее посещение", 400);

}



$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );
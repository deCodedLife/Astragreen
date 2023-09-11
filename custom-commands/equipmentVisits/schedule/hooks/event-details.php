<?php

/**
 * Определение цвета Записи
 */
switch ( $event[ "status" ][ "value" ] ) {

    case "planning":
        $event[ "color" ] = "primary";
        break;

    case "ended":
        $event[ "color" ] = "danger";
        break;

    case "process":
        $event[ "color" ] = "warning";
        break;



} // switch. $event[ "status" ][ "value" ]


/**
 * Определение иконки Записи
 */

if ( $event[ "is_payed" ] ) $event[ "icons" ][] = "shopping-basket";
if ( $event[ "is_earlier" ] ) $event[ "icons" ][] = "time";


/**
 * Получение детальной информации о пациенте
 */
$clientDetail = $API->DB->from( "clients" )
    ->where( "id", $event[ "clients_id" ][ 0 ][ "value" ] )
    ->limit( 1 )
    ->fetch();

/**
 * Получение времени начала и конца Записи
 */
$eventTime =
    date( "H:i", strtotime( $event[ "start_at" ] ) ) . " - " .
    date( "H:i", strtotime( $event[ "end_at" ] ) );

/**
 * Получение пациента
 */
$eventClient = "№ " . $clientDetail[ "id" ] . " " . $clientDetail[ "last_name" ] . " " . mb_substr( $clientDetail[ "first_name" ], 0, 1, "UTF-8" ) . ". " . mb_substr( $clientDetail[ "patronymic" ], 0, 1, "UTF-8") . ".";
$eventClientDetails = "№ " . $clientDetail[ "id" ] . " " . $clientDetail[ "last_name" ] . " " . $clientDetail[ "first_name" ] . " " . $clientDetail[ "patronymic" ];

/**
 * Получение услуг
 */
$eventServices = "";
foreach ( $event[ "services_id" ] as $eventService ) $eventServices .= $eventService[ "title" ] . ", ";
if ( $eventServices ) $eventServices = substr( $eventServices, 0, -2 );


/**
 * Заполнение описания Записи
 */
$eventDescription = [ $eventClient, $eventTime ];

/**
 * Заполнение детальной информации о Записи к врачу
 */

$eventDetails = [
    [
        "icon" => "schedule",
        "value" => $eventTime
    ],
    [
        "icon" => "customers",
        "value" => $eventClientDetails
    ],
    [
        "icon" => "conversation",
        "value" => sprintf("+%s (%s) %s-%s-%s",
            substr( $clientDetail[ "phone" ], 0, 1 ),
            substr( $clientDetail[ "phone" ], 1, 3 ),
            substr( $clientDetail[ "phone" ], 4, 3 ),
            substr( $clientDetail[ "phone" ], 7, 2 ),
            substr( $clientDetail[ "phone" ], 9 )
        )
    ],
    [
        "icon" => "user",
        "value" => $event[ "equipment_id" ][ 0 ][ "title" ]
    ],
    [
        "icon" => "stethoscope",
        "value" => $eventServices
    ],
    [
        "icon" => "",
        "value" => $event[ "price" ] . "₽"
    ]

];
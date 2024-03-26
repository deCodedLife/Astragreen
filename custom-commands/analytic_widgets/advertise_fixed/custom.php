<?php

/**
 * @file
 * Отчет "Рекламные источники"
 */


$API->returnResponse(
    [
        [
           "value" => number_format( intval( $reportCache[ "data" ][ "price" ] ), 0, '.', ' ' ),
           "description" => "Прибыль",
           "icon" => "",
           "prefix" => "₽",
           "postfix" => [
               "icon" => "",
               "value" => "",
               "background" => ""
           ],
           "type" => "char",
           "background" => "",
           "detail" => []
        ]
    ]
);

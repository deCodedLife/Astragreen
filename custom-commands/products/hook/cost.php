<?php

/**
 * @file
 * Хуки на Закупку
 */
$formFieldsUpdate = [];

$margin = (($requestData->price - $requestData->cost) / $requestData->cost) * 100;

$formFieldsUpdate[ "margin" ][ "value" ] = (string)round( $margin );

$API->returnResponse( $formFieldsUpdate );
<?php

/**
 * Фильтр документов при печати
 */
if ( $requestData->context->block == "print" ) {

    $requestData->is_system = 'Y';
    $requestData->is_general = 'Y';

    if ( $API::$userDetail->role_id == 6 ) {

        $requestData->is_system = 'N';
        $requestData->is_general = 'N';
        $requestData->owners_id[] = $API::$userDetail->id;

    }

}

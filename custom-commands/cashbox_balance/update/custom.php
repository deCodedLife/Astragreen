<?php



/**
 * Получение списка филлиалов
 */
$listOfStores = $API->DB->from( "stores" )
    ->where( "is_active", 'Y' );


/**
 * Запись данных по каждому филлиалу
 */
foreach ( $listOfStores as $store ) {

    /**
     * Получение списка операций
     * TODO: Добавить фильтр по дате
     */
    $payments = mysqli_query(
        $API->DB_connection,
        "SELECT * FROM sales WHERE pay_type = \"sell\" AND is_active = \"Y\" AND store_id = {$store[ "id" ]}"
    );

    /**
     * Получение исходящего остатка с предыдущего дня
     * TODO: Добавить расходники
     */
    $incomeBalance = $API->DB->from( "cashboxBalances" )
        ->where( "store_id", $store[ "id" ] )
        ->orderBy( "created_at DESC" )
        ->limit( 1 )
        ->fetch();


    /**
     * Подсчёт исходящего остатка
     */
    $paymentsSummary = 0;
    $incomeBalance = $incomeBalance[ "balance" ] ?? 0;

    foreach ( $payments as $payment ) $paymentsSummary += $payment[ "summary" ];
    $paymentsSummary += $incomeBalance;


    /**
     * Запись состояния
     */
    $API->DB->insertInto( "cashboxBalances" )
        ->values([
            "balance" => $paymentsSummary,
            "store_id" => $store[ "id" ]
        ])
        ->execute();

} // foreach ( $listOfStores as $store ) {
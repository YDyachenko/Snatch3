<?php

return array(
    'application' => array(
        'displayExceptions' => true,
        'viewHelpers'       => array(
            'accountBalance' => 'Application\View\Helper\AccountBalance',
            'currencySymbol' => 'Application\View\Helper\CurrencySymbol',
            'showIncome'     => 'Application\View\Helper\showIncome',
        ),
    ),
    'database'    => array(
        'user'     => 'bank',
        'password' => '',
        'host'     => 'localhost',
        'port'     => '',
        'dbname'   => 'bank',
    ),
    'rates'       => array(
        'rub>usd' => 1 / 36.35,
        'usd>rub' => 35.60,
        'rub>rub' => 1,
        'usd>usd' => 1,
    ),
);

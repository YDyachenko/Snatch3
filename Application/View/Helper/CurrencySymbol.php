<?php

namespace Application\View\Helper;

use Core\View\Helper\AbstractHelper;

class CurrencySymbol extends AbstractHelper
{
    public function __invoke($currency)
    {
        switch ($currency) {
            case 'rub': 
                $symbol = 'Rub';
                break;
            case 'usd':
                $symbol = '$';
                break;
        }
        return $symbol;
    }
}

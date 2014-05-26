<?php

namespace Application\View\Helper;

use Core\View\Helper\AbstractHelper;
use Application\Model\Account;

class AccountBalance extends AbstractHelper
{
    public function __invoke(Account $account)
    {
        $symbol = $this->getView()->currencySymbol($account->getCurrency());
        
        return $account->getBalance() . "&nbsp;" . $symbol;
    }
}

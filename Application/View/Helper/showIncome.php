<?php

namespace Application\View\Helper;

use Core\View\Helper\AbstractHelper;

class ShowIncome extends AbstractHelper
{

    public function __invoke(array $income)
    {
        $return = array();

        foreach ($income as $item) {
            $string = "{$item['sum']} {$this->view->currencySymbol($item['currency'])} from {$item['from']}";
            if (!empty($item['description'])) {
                $string .= "<i>({$item['description']})</i>";
            }
            
            $return[] = $string;
        }
        
        $template = '<div class="alert alert-success"><strong>Income!</strong><br>%s</div>';
        return sprintf($template, implode("<br>", $return));
    }

}

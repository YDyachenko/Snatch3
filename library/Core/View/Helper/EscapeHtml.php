<?php

namespace Core\View\Helper;

class Escapehtml extends AbstractHelper
{
    public function __invoke($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
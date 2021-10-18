<?php

namespace Pharaoh\Invoice\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Invoice
 * @package Pharaoh\Invoice\Facades
 * @see \Pharaoh\Invoice\Invoice
 */
class Invoice extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        // 回傳 alias 的名稱
        return 'invoice';
    }
}

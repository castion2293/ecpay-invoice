<?php

namespace Pharaoh\Invoice;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/invoice.php', 'invoice');

        $this->publishes(
            [
                __DIR__ . '/../config/invoice.php' => config_path('invoice.php')
            ],
            'invoice-config'
        );
    }

    public function register()
    {
        parent::register();

        $loader = AliasLoader::getInstance();
        $loader->alias('invoice', 'Pharaoh\Invoice\Invoice');
    }
}

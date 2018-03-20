<?php

namespace TaylorNetwork\VariableReplacer;

use Illuminate\Support\ServiceProvider;

class VariableReplacerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/variable_replacer.php' => config_path('variable_replacer.php')
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/variable_replacer.php', 'variable_replacer');
    }
}
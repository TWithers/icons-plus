<?php

namespace Twithers\IconsPlus;

use Statamic\Providers\AddonServiceProvider;
use TWithers\IconsPlus\Fieldtypes\IconsPlus;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    protected $fieldtypes = [
        IconsPlus::class,
    ];

    public function bootAddon()
    {

    }
}

<?php

namespace Twithers\StatamicIconsPlus\Tests;

use Twithers\StatamicIconsPlus\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}

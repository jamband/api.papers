<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected Repository $appConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appConfig = $this->app['config'];
    }
}

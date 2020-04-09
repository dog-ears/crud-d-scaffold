<?php

namespace Tests\Unit;

use Orchestra\Testbench\TestCase;

/* ------------------------------
PHP Unit Test
------------------------------ */

class GeneratorsServiceProviderTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return ['DogEars\CrudDScaffold\GeneratorsServiceProvider'];
    }

    public function testBasicTest()
    {
        $this->assertTrue(true);
    }
}
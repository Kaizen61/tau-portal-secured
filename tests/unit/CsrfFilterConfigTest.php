<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class CsrfFilterConfigTest extends CIUnitTestCase
{
    public function testCsrfFilterRunsForAllPostRequests(): void
    {
        $filters = new Config\Filters();

        foreach (['POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
            $this->assertSame(['csrf'], $filters->methods[$method]);
        }
    }
}

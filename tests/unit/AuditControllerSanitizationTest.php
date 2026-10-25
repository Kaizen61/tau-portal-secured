<?php

use App\Controllers\Admin\AuditController;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AuditControllerSanitizationTest extends CIUnitTestCase
{
    public function testAuditOutputOmitsIpAndHashFieldsAndRedactsAddressesFromText(): void
    {
        $controller = (new ReflectionClass(AuditController::class))->newInstanceWithoutConstructor();
        $sanitize = new ReflectionMethod($controller, 'sanitizeAuditRows');
        $rows = [[
            'ip_address' => '192.168.1.1',
            'details' => 'Denied request from 10.0.0.4 and 8.8.8.8',
            'user_agent' => 'Client 192.168.1.1',
            'prev_hash' => str_repeat('a', 64),
            'entry_hash' => str_repeat('b', 64),
        ]];

        $sanitized = $sanitize->invoke($controller, $rows);

        $this->assertArrayNotHasKey('ip_address', $sanitized[0]);
        $this->assertArrayNotHasKey('prev_hash', $sanitized[0]);
        $this->assertArrayNotHasKey('entry_hash', $sanitized[0]);
        $this->assertStringNotContainsString('10.0.0.4', $sanitized[0]['details']);
        $this->assertStringNotContainsString('8.8.8.8', $sanitized[0]['details']);
        $this->assertStringNotContainsString('192.168.1.1', $sanitized[0]['user_agent']);
        $this->assertSame('192.168.1.1', $rows[0]['ip_address']);
    }
}

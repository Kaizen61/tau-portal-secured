<?php

use App\Libraries\SchoolYear;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class SchoolYearTest extends CIUnitTestCase
{
    public function testNewSchoolYearBecomesAvailableInJune(): void
    {
        $timezone = new \DateTimeZone('Asia/Manila');
        $beforeJune = new \DateTimeImmutable('2026-05-31', $timezone);
        $inJune = new \DateTimeImmutable('2026-06-01', $timezone);

        $this->assertSame(['2023-2024', '2024-2025', '2025-2026'], SchoolYear::available($beforeJune));
        $this->assertSame('2025-2026', SchoolYear::current($beforeJune));
        $this->assertSame(['2023-2024', '2024-2025', '2025-2026', '2026-2027'], SchoolYear::available($inJune));
        $this->assertSame('2026-2027', SchoolYear::current($inJune));
    }
}

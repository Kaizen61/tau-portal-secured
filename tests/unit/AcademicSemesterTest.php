<?php

use App\Libraries\AcademicSemester;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AcademicSemesterTest extends CIUnitTestCase
{
    public function testSemesterPeriodsFollowTheAcademicCalendar(): void
    {
        $this->assertSame('1st', AcademicSemester::current(new DateTimeImmutable('2026-08-01')));
        $this->assertSame('1st', AcademicSemester::current(new DateTimeImmutable('2026-12-31')));
        $this->assertSame('2nd', AcademicSemester::current(new DateTimeImmutable('2027-01-01')));
        $this->assertSame('2nd', AcademicSemester::current(new DateTimeImmutable('2027-05-31')));
        $this->assertNull(AcademicSemester::current(new DateTimeImmutable('2027-06-01')));
        $this->assertNull(AcademicSemester::current(new DateTimeImmutable('2027-07-31')));
    }
}

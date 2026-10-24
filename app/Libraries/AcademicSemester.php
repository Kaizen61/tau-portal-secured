<?php

namespace App\Libraries;

use DateTimeImmutable;
use DateTimeZone;

final class AcademicSemester
{
    public static function current(?DateTimeImmutable $today = null): ?string
    {
        $today ??= new DateTimeImmutable('now', new DateTimeZone('Asia/Manila'));
        $month = (int) $today->format('n');

        if ($month >= 8) {
            return '1st';
        }

        if ($month <= 5) {
            return '2nd';
        }

        return null;
    }
}

<?php

namespace App\Libraries;

use DateTimeImmutable;
use DateTimeZone;

final class SchoolYear
{
    private const FIRST_YEAR = 2023;

    public static function available(?DateTimeImmutable $today = null): array
    {
        $currentStartYear = self::currentStartYear($today);
        $schoolYears = [];

        for ($startYear = self::FIRST_YEAR; $startYear <= $currentStartYear; $startYear++) {
            $schoolYears[] = $startYear . '-' . ($startYear + 1);
        }

        return $schoolYears;
    }

    public static function current(?DateTimeImmutable $today = null): string
    {
        $startYear = max(self::FIRST_YEAR, self::currentStartYear($today));

        return $startYear . '-' . ($startYear + 1);
    }

    public static function isAvailable(string $schoolYear): bool
    {
        return in_array($schoolYear, self::available(), true);
    }

    private static function currentStartYear(?DateTimeImmutable $today): int
    {
        $today ??= new DateTimeImmutable('now', new DateTimeZone('Asia/Manila'));
        $year = (int) $today->format('Y');

        return (int) $today->format('n') >= 6 ? $year : $year - 1;
    }
}

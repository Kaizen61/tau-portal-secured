<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Libraries\SchoolYear;
use App\Models\AnnouncementModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $studentId = (int) session()->get('user_id');
        $schoolYear = SchoolYear::current();
        $gradeRows = db_connect()->table('grades')
            ->select('grades.school_year, grades.grade, subjects.units, subjects.semester')
            ->join('subjects', 'subjects.id = grades.subject_id')
            ->where('grades.student_id', $studentId)
            ->orderBy('grades.school_year', 'DESC')
            ->orderBy('subjects.semester', 'ASC')
            ->get()
            ->getResultArray();

        $currentWeightedTotal = 0.0;
        $currentGradedUnits = 0;
        $pendingGrades = 0;
        $unitsEnrolled = 0;
        $enrolledSubjects = 0;
        $overallWeightedTotal = 0.0;
        $overallGradedUnits = 0;
        $semesterTotals = [];

        foreach ($gradeRows as $row) {
            $units = (int) $row['units'];
            $isCurrentYear = $row['school_year'] === $schoolYear;
            if ($isCurrentYear) {
                $enrolledSubjects++;
                $unitsEnrolled += $units;
            }

            if ($row['grade'] === null) {
                if ($isCurrentYear) {
                    $pendingGrades++;
                }
                continue;
            }

            $weightedGrade = (float) $row['grade'] * $units;
            $overallWeightedTotal += $weightedGrade;
            $overallGradedUnits += $units;

            $termKey = $row['school_year'] . '|' . $row['semester'];
            if (!isset($semesterTotals[$termKey])) {
                $semesterTotals[$termKey] = [
                    'school_year' => $row['school_year'],
                    'semester' => $row['semester'],
                    'weighted_total' => 0.0,
                    'graded_units' => 0,
                ];
            }
            $semesterTotals[$termKey]['weighted_total'] += $weightedGrade;
            $semesterTotals[$termKey]['graded_units'] += $units;

            if ($isCurrentYear) {
                $currentWeightedTotal += $weightedGrade;
                $currentGradedUnits += $units;
            }
        }

        $semesterTotals = array_values($semesterTotals);
        usort($semesterTotals, static function (array $left, array $right): int {
            $schoolYearOrder = strcmp($left['school_year'], $right['school_year']);
            if ($schoolYearOrder !== 0) {
                return $schoolYearOrder;
            }

            return strcmp($left['semester'], $right['semester']);
        });

        $gwaTrend = [];
        foreach (array_slice($semesterTotals, -6) as $term) {
            $gwaTrend[] = [
                'label' => $term['school_year'] . ' · ' . $term['semester'] . ' Sem',
                'gwa' => round($term['weighted_total'] / $term['graded_units'], 2),
            ];
        }

        $announcements = (new AnnouncementModel())
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(3);

        $data = [
            'pageTitle' => 'Student Dashboard - TAU Student Portal',
            'activePage' => 'dashboard',
            'eyebrow' => 'Student',
            'pageHeading' => 'My Dashboard',
            'schoolYear' => $schoolYear,
            'currentGwa' => $currentGradedUnits > 0 ? round($currentWeightedTotal / $currentGradedUnits, 2) : null,
            'pendingGrades' => $pendingGrades,
            'unitsEnrolled' => $unitsEnrolled,
            'enrolledSubjects' => $enrolledSubjects,
            'overallGwa' => $overallGradedUnits > 0 ? round($overallWeightedTotal / $overallGradedUnits, 2) : null,
            'gwaTrend' => $gwaTrend,
            'announcements' => $announcements,
        ];
        return view('student/dashboard', $data);
    }
}

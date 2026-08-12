<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\GradeModel;

class GradeController extends BaseController
{
    public function index()
    {
        $grades = new GradeModel();
        $studentId = session()->get('user_id');

        $schoolYear = $this->request->getGet('school_year');
        $semester = $this->request->getGet('semester');
        $semester = in_array($semester, ['1st', '2nd'], true) ? $semester : null;
        $schoolYear = $schoolYear && preg_match('/^\d{4}-\d{4}$/', $schoolYear) ? $schoolYear : null;

        // FIX per instruction: GWA now lives here, computed per semester
        // (units-weighted average of graded subjects) - see GradeModel.
        $groups = $grades->getGradesGroupedBySemester($studentId, $schoolYear, $semester);

        $schoolYears = $grades->select('school_year')->where('student_id', $studentId)
            ->distinct()->orderBy('school_year', 'DESC')->findAll();

        $data = [
            'pageTitle' => 'My Grades - TAU Student Portal',
            'activePage' => 'grades',
            'eyebrow' => 'Student',
            'pageHeading' => 'My Grades',
            'groups' => $groups,
            'schoolYears' => $schoolYears,
            'filterSy' => $schoolYear,
            'filterSem' => $semester,
        ];
        return view('student/grades', $data);
    }
}

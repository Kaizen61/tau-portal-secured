<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\GradeModel;
use App\Models\UserModel;

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

    public function printReport()
    {
        $studentId = (int) session()->get('user_id');
        $schoolYear = $this->request->getGet('school_year');
        $semester = $this->request->getGet('semester');
        $semester = in_array($semester, ['1st', '2nd'], true) ? $semester : null;
        $schoolYear = $schoolYear && preg_match('/^\d{4}-\d{4}$/', $schoolYear) ? $schoolYear : null;

        if (!$schoolYear || !$semester) {
            return $this->response->setStatusCode(400)->setBody(
                view('errors_custom/generic', ['message' => 'School year and semester are required to print a grade report.'])
            );
        }

        $groups = (new GradeModel())->getGradesGroupedBySemester($studentId, $schoolYear, $semester);
        $group = $groups ? reset($groups) : null;
        if (!$group) {
            return $this->response->setStatusCode(404)->setBody(
                view('errors_custom/generic', ['message' => 'No grade records found for that semester.'])
            );
        }

        return view('student/print_grade_report', [
            'printTitle' => 'Grade Report - TAU Student Portal',
            'documentTitle' => 'Report of Grades',
            'printDocumentClass' => 'signatory-print-sheet grade-report-print-sheet',
            'printedAt' => date('F j, Y g:i A'),
            'autoPrint' => false,
            'profile' => (new UserModel())->find($studentId),
            'group' => $group,
        ]);
    }
}

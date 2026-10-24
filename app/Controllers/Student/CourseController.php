<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\GradeModel;
use App\Models\UserModel;

class CourseController extends BaseController
{
    public function index()
    {
        $studentId = (int) session()->get('user_id');
        [$schoolYear, $semester] = $this->termFilters();

        $data = [
            'pageTitle' => 'My Courses - TAU Student Portal',
            'activePage' => 'courses',
            'eyebrow' => 'Student',
            'pageHeading' => 'My Courses',
            'groups' => $this->groupedCourses($studentId, $schoolYear, $semester),
            'schoolYears' => (new GradeModel())->select('school_year')->where('student_id', $studentId)->distinct()->orderBy('school_year', 'DESC')->findAll(),
            'filterSy' => $schoolYear,
            'filterSem' => $semester,
        ];
        return view('student/courses', $data);
    }

    public function printCor()
    {
        $studentId = (int) session()->get('user_id');
        [$schoolYear, $semester] = $this->termFilters();

        if (!$schoolYear || !$semester) {
            return $this->response->setStatusCode(400)->setBody(
                view('errors_custom/generic', ['message' => 'School year and semester are required to print a Certificate of Registration.'])
            );
        }

        $groups = $this->groupedCourses($studentId, $schoolYear, $semester);
        $group = $groups ? reset($groups) : null;
        if (!$group) {
            return $this->response->setStatusCode(404)->setBody(
                view('errors_custom/generic', ['message' => 'No enrolled subjects found for that semester.'])
            );
        }

        return view('student/print_cor', [
            'printTitle' => 'Certificate of Registration - TAU Student Portal',
            'documentTitle' => 'Certificate of Registration',
            'printDocumentClass' => 'signatory-print-sheet cor-print-sheet',
            'printedAt' => date('F j, Y g:i A'),
            'autoPrint' => false,
            'profile' => (new UserModel())->find($studentId),
            'group' => $group,
        ]);
    }

    protected function termFilters(): array
    {
        $schoolYear = $this->request->getGet('school_year');
        $semester = $this->request->getGet('semester');
        $semester = in_array($semester, ['1st', '2nd'], true) ? $semester : null;
        $schoolYear = $schoolYear && preg_match('/^\d{4}-\d{4}$/', $schoolYear) ? $schoolYear : null;

        return [$schoolYear, $semester];
    }

    protected function groupedCourses(int $studentId, ?string $schoolYear, ?string $semester): array
    {
        $grades = new GradeModel();
        $builder = $grades->select('grades.school_year, subjects.subject_code, subjects.subject_name, subjects.units, subjects.year_level, subjects.semester, users.full_name as teacher_name')
            ->join('subjects', 'subjects.id = grades.subject_id')
            ->join('users', 'users.id = subjects.teacher_id', 'left')
            ->where('grades.student_id', $studentId);
        if ($schoolYear) {
            $builder->where('grades.school_year', $schoolYear);
        }
        if ($semester) {
            $builder->where('subjects.semester', $semester);
        }

        $rows = $builder->orderBy('grades.school_year', 'DESC')->orderBy('subjects.semester')->orderBy('subjects.subject_code')->findAll();

        $groups = [];
        foreach ($rows as $row) {
            $key = $row['school_year'] . '|' . $row['semester'];
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'school_year' => $row['school_year'],
                    'semester' => $row['semester'],
                    'year_level' => $row['year_level'],
                    'subjects' => [],
                    'total_units' => 0,
                ];
            }
            $groups[$key]['subjects'][] = $row;
            $groups[$key]['total_units'] += (int) $row['units'];
        }

        return $groups;
    }
}

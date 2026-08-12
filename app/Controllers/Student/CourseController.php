<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\GradeModel;

class CourseController extends BaseController
{
    public function index()
    {
        $grades = new GradeModel();
        $studentId = session()->get('user_id');

        $schoolYear = $this->request->getGet('school_year');
        $semester = $this->request->getGet('semester');
        $semester = in_array($semester, ['1st', '2nd'], true) ? $semester : null;
        $schoolYear = $schoolYear && preg_match('/^\d{4}-\d{4}$/', $schoolYear) ? $schoolYear : null;

        $builder = $grades->select('grades.school_year, subjects.subject_code, subjects.subject_name, subjects.units, subjects.year_level, subjects.semester, users.full_name as teacher_name')
            ->join('subjects', 'subjects.id = grades.subject_id')
            ->join('users', 'users.id = subjects.teacher_id', 'left')
            ->where('grades.student_id', $studentId);
        if ($schoolYear) $builder->where('grades.school_year', $schoolYear);
        if ($semester) $builder->where('subjects.semester', $semester);

        $data = [
            'pageTitle' => 'My Courses - TAU Student Portal',
            'activePage' => 'courses',
            'eyebrow' => 'Student',
            'pageHeading' => 'My Courses',
            'courses' => $builder->orderBy('grades.school_year', 'DESC')->orderBy('subjects.semester')->orderBy('subjects.subject_code')->findAll(),
            'schoolYears' => $grades->select('school_year')->where('student_id', $studentId)->distinct()->orderBy('school_year', 'DESC')->findAll(),
            'filterSy' => $schoolYear,
            'filterSem' => $semester,
        ];
        return view('student/courses', $data);
    }
}

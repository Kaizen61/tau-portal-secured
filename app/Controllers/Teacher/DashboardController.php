<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\GradeModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $subjects = new SubjectModel();
        $grades = new GradeModel();
        $teacherId = session()->get('user_id');

        $yearLevel = $this->request->getGet('year_level');
        $semester = $this->request->getGet('semester');
        $yearLevel = in_array($yearLevel, ['1','2','3','4'], true) ? $yearLevel : null;
        $semester = in_array($semester, ['1st','2nd'], true) ? $semester : null;

        $subjectList = $subjects->forTeacher($teacherId, $yearLevel, $semester);
        foreach ($subjectList as &$s) {
            $s['student_count'] = $grades->where('subject_id', $s['id'])->countAllResults();
        }

        $data = [
            'pageTitle' => 'Teacher Dashboard - TAU Student Portal',
            'activePage' => 'dashboard',
            'eyebrow' => 'Faculty',
            'pageHeading' => 'Faculty Dashboard',
            'subjectList' => $subjectList,
            'filterYear' => $yearLevel,
            'filterSem' => $semester,
        ];
        return view('teacher/dashboard', $data);
    }
}

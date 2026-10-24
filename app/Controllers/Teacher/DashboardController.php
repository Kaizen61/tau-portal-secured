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
        $teacherId = session()->get('user_id');

        $yearLevel = $this->request->getGet('year_level');
        $semester = $this->request->getGet('semester');
        $yearLevel = in_array($yearLevel, ['1','2','3','4'], true) ? $yearLevel : null;
        $semester = in_array($semester, ['1st','2nd'], true) ? $semester : null;

        $subjectList = $subjects->forTeacher($teacherId, $yearLevel, $semester);
        $totalEnrolled = 0;
        $totalGraded = 0;
        foreach ($subjectList as &$s) {
            $s['student_count'] = (new GradeModel())
                ->where('subject_id', $s['id'])
                ->countAllResults();
            $s['graded_count'] = (new GradeModel())
                ->where('subject_id', $s['id'])
                ->where('grade IS NOT NULL', null, false)
                ->countAllResults();
            $s['pending_count'] = $s['student_count'] - $s['graded_count'];
            $totalEnrolled += $s['student_count'];
            $totalGraded += $s['graded_count'];
        }
        unset($s);

        $data = [
            'pageTitle' => 'Teacher Dashboard - TAU Student Portal',
            'activePage' => 'dashboard',
            'eyebrow' => 'Faculty',
            'pageHeading' => 'Faculty Dashboard',
            'subjectList' => $subjectList,
            'subjectCount' => count($subjectList),
            'totalEnrolled' => $totalEnrolled,
            'totalGraded' => $totalGraded,
            'totalPending' => $totalEnrolled - $totalGraded,
            'filterYear' => $yearLevel,
            'filterSem' => $semester,
        ];
        return view('teacher/dashboard', $data);
    }
}

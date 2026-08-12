<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\GradeModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $grades = new GradeModel();
        $studentId = session()->get('user_id');

        // FIX per instruction: GWA no longer lives on the dashboard - only
        // the enrolled-subject count stays here. GWA is computed and shown
        // per semester on the My Grades page instead.
        $data = [
            'pageTitle' => 'Student Dashboard - TAU Student Portal',
            'activePage' => 'dashboard',
            'eyebrow' => 'Student',
            'pageHeading' => 'My Dashboard',
            'subjectCount' => $grades->where('student_id', $studentId)->countAllResults(),
        ];
        return view('student/dashboard', $data);
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SubjectModel;
use App\Models\GradeModel;
use App\Models\ActivityLogModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $users = new UserModel();
        $subjects = new SubjectModel();
        $grades = new GradeModel();
        $logs = new ActivityLogModel();

        $data = [
            'pageTitle' => 'Admin Dashboard - TAU Student Portal',
            'activePage' => 'dashboard',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Dashboard Overview',
            'studentCount' => $users->where('role', 'student')->countAllResults(),
            'teacherCount' => $users->where('role', 'teacher')->countAllResults(),
            'subjectCount' => $subjects->countAllResults(),
            'pendingCount' => $grades->where('grade', null)->countAllResults(),
            'highSeverityCount' => $logs->recentHighSeverityCount(),
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * FIX: AJAX polling endpoint for the bento dashboard - refreshes the
     * stat tiles and the high-severity alert tile without a page reload.
     */
    public function poll()
    {
        $users = new UserModel();
        $subjects = new SubjectModel();
        $grades = new GradeModel();
        $logs = new ActivityLogModel();

        return $this->response->setJSON([
            'studentCount' => $users->where('role', 'student')->countAllResults(),
            'teacherCount' => $users->where('role', 'teacher')->countAllResults(),
            'subjectCount' => $subjects->countAllResults(),
            'pendingCount' => $grades->where('grade', null)->countAllResults(),
            'highSeverityCount' => $logs->recentHighSeverityCount(),
        ]);
    }
}
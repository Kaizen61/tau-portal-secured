<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;

class StaticController extends BaseController
{
    public function attendance()
    {
        return view('teacher/attendance', [
            'pageTitle' => 'Attendance - TAU Student Portal',
            'activePage' => 'attendance',
            'eyebrow' => 'Faculty',
            'pageHeading' => 'Attendance',
        ]);
    }

    public function assignments()
    {
        return view('teacher/assignments', [
            'pageTitle' => 'Assignments - TAU Student Portal',
            'activePage' => 'assignments',
            'eyebrow' => 'Faculty',
            'pageHeading' => 'Assignments',
        ]);
    }

    public function schedule()
    {
        return view('teacher/schedule', [
            'pageTitle' => 'Class Schedule - TAU Student Portal',
            'activePage' => 'schedule',
            'eyebrow' => 'Faculty',
            'pageHeading' => 'Class Schedule',
        ]);
    }

    public function announcements()
    {
        return view('teacher/announcements', [
            'pageTitle' => 'Announcements - TAU Student Portal',
            'activePage' => 'announcements',
            'eyebrow' => 'Faculty',
            'pageHeading' => 'Course Announcements',
        ]);
    }
}

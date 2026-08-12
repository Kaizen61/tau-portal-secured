<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;

class StaticController extends BaseController
{
    public function attendance()
    {
        return view('student/attendance', ['pageTitle'=>'Attendance - TAU Student Portal','activePage'=>'attendance','eyebrow'=>'Student','pageHeading'=>'My Attendance']);
    }

    public function assignments()
    {
        return view('student/assignments', ['pageTitle'=>'Assignments - TAU Student Portal','activePage'=>'assignments','eyebrow'=>'Student','pageHeading'=>'My Assignments']);
    }

    public function calendar()
    {
        return view('student/calendar', ['pageTitle'=>'Calendar - TAU Student Portal','activePage'=>'calendar','eyebrow'=>'Student','pageHeading'=>'Calendar']);
    }

    public function announcements()
    {
        return view('student/announcements', ['pageTitle'=>'Announcements - TAU Student Portal','activePage'=>'announcements','eyebrow'=>'Student','pageHeading'=>'Announcements']);
    }
}

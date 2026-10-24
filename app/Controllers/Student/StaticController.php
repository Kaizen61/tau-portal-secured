<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class StaticController extends BaseController
{
    public function announcements()
    {
        return view('student/announcements', [
            'pageTitle' => 'Announcements - TAU Student Portal',
            'activePage' => 'announcements',
            'eyebrow' => 'Student',
            'pageHeading' => 'Announcements',
            'announcements' => (new AnnouncementModel())->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->findAll(),
        ]);
    }
}

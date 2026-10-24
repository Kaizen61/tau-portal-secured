<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class StaticController extends BaseController
{
    public function announcements()
    {
        return view('teacher/announcements', [
            'pageTitle' => 'Announcements - TAU Student Portal',
            'activePage' => 'announcements',
            'eyebrow' => 'Faculty',
            'pageHeading' => 'School Announcements',
            'announcements' => (new AnnouncementModel())->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->findAll(),
        ]);
    }
}

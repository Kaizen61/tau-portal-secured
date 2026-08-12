<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class StaticController extends BaseController
{
    public function announcements()
    {
        return view('admin/announcements', [
            'pageTitle' => 'Announcements - TAU Student Portal',
            'activePage' => 'announcements',
            'eyebrow' => 'Administration',
            'pageHeading' => 'School Announcements',
        ]);
    }
}

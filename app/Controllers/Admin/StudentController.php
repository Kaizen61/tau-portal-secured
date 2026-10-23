<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class StudentController extends BaseController
{
    public function index()
    {
        $users = new UserModel();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $users->where('role', 'student');
        if ($q !== '') {
            $builder->groupStart()
                ->like('full_name', $q)
                ->orLike('student_number', $q)
                ->orLike('college', $q)
                ->groupEnd();
        }

        $data = [
            'pageTitle' => 'Students - TAU Student Portal',
            'activePage' => 'students',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Students',
            'students' => $builder->orderBy('full_name')->findAll(),
            'q' => $q,
        ];
        return view('admin/students', $data);
    }
}

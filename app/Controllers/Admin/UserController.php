<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\ActivityLogger;

class UserController extends BaseController
{
    public function index()
    {
        $users = new UserModel();
        $data = [
            'pageTitle' => 'Manage Users - TAU Student Portal',
            'activePage' => 'users',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Manage User Accounts',
            'users' => $users->where('username !=', 'backup_admin')->orderBy('role')->orderBy('full_name')->findAll(),
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ];
        return view('admin/users', $data);
    }

    public function create()
    {
        $users = new UserModel();
        $role = $this->request->getPost('role');
        $isStudent = $role === 'student';

        // FIX (A10): CI4's validation service - the same centralized,
        // explicit rule-checking approach used throughout this app.
        $rules = [
            'full_name' => 'required|min_length[2]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[student,teacher,admin]',
        ];
        if ($isStudent) {
            $rules['student_number'] = 'required|regex_match[/^\d{10}$/]|is_unique[users.student_number]';
        } else {
            $rules['username'] = 'required|is_unique[users.username]';
        }

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('admin/users');
        }

        if ($isStudent) {
            $studentNumber = $this->request->getPost('student_number');
            $year = (int) substr($studentNumber, 0, 4);
            if ($year > (int) date('Y') || $year < 2000) {
                session()->setFlashdata('error', "Student number year ($year) cannot be later than the current year.");
                return redirect()->to('admin/users');
            }
        }

        $id = $users->insert([
            'username' => $isStudent ? null : $this->request->getPost('username'),
            'student_number' => $isStudent ? $this->request->getPost('student_number') : null,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $role,
            'full_name' => $this->request->getPost('full_name'),
            'college' => $this->request->getPost('college'),
            'year_level' => $isStudent ? $this->request->getPost('year_level') : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        (new ActivityLogger())->log(session()->get('user_id'), session()->get('username'), 'ACCOUNT_CREATED',
            "Created $role account (user #$id)", 'INFO');

        session()->setFlashdata('saved', 'User account created.');
        return redirect()->to('admin/users');
    }
}

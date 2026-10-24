<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GradeModel;
use App\Libraries\ActivityLogger;
use App\Libraries\AcademicProgram;

class ProfileController extends BaseController
{
    public function index()
    {
        $users = new UserModel();
        $data = [
            'pageTitle' => 'My Profile - TAU Student Portal',
            'activePage' => 'profile',
            'eyebrow' => 'Student',
            'pageHeading' => 'My Profile',
            'profile' => $users->find(session()->get('user_id')),
            'programNames' => array_merge(...array_values(AcademicProgram::PROGRAMS_BY_COLLEGE)),
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ];
        return view('student/profile', $data);
    }

    public function printRecord()
    {
        $studentId = (int) session()->get('user_id');
        $records = (new GradeModel())
            ->select('grades.school_year, grades.grade, subjects.subject_code, subjects.subject_name, subjects.units, subjects.semester')
            ->join('subjects', 'subjects.id = grades.subject_id')
            ->where('grades.student_id', $studentId)
            ->orderBy('grades.school_year', 'DESC')
            ->orderBy('subjects.semester')
            ->orderBy('subjects.subject_code')
            ->findAll();

        return view('student/print_student_record', [
            'printTitle' => 'Student Record - TAU Student Portal',
            'documentTitle' => 'Student Record',
            'printDocumentClass' => 'signatory-print-sheet',
            'printedAt' => date('F j, Y g:i A'),
            'autoPrint' => false,
            'profile' => (new UserModel())->find($studentId),
            'programNames' => array_merge(...array_values(AcademicProgram::PROGRAMS_BY_COLLEGE)),
            'records' => $records,
        ]);
    }

    public function update()
    {
        $users = new UserModel();
        $studentId = session()->get('user_id');

        if (!$this->validate(['full_name' => 'required|min_length[2]'])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('student/profile');
        }

        $fullName = $this->request->getPost('full_name');
        $users->update($studentId, ['full_name' => $fullName]);
        session()->set('full_name', $fullName);

        (new ActivityLogger())->log($studentId, session()->get('username'), 'PROFILE_UPDATED', 'Updated full name', 'INFO');
        session()->setFlashdata('saved', 'Profile updated.');
        return redirect()->to('student/profile');
    }

    public function changePassword()
    {
        $users = new UserModel();
        $studentId = session()->get('user_id');
        $user = $users->find($studentId);

        $current = $this->request->getPost('current_password');
        $new = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        if (!password_verify($current, $user['password'])) {
            session()->setFlashdata('error', 'Current password is incorrect.');
        } elseif (strlen($new) < 8) {
            session()->setFlashdata('error', 'New password must be at least 8 characters.');
        } elseif ($new !== $confirm) {
            session()->setFlashdata('error', 'New password and confirmation do not match.');
        } else {
            $users->update($studentId, ['password' => password_hash($new, PASSWORD_DEFAULT)]);
            (new ActivityLogger())->log($studentId, session()->get('username'), 'PASSWORD_CHANGED', null, 'INFO');
            session()->setFlashdata('saved', 'Password changed successfully.');
        }

        return redirect()->to('student/profile');
    }
}

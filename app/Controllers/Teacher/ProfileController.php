<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    public function index()
    {
        $profile = (new UserModel())->find(session()->get('user_id'));
        if (!$profile || $profile['role'] !== 'teacher') {
            return redirect()->to('logout');
        }

        return view('teacher/profile', [
            'pageTitle' => 'My Profile - TAU Student Portal',
            'activePage' => 'profile',
            'eyebrow' => 'Faculty',
            'pageHeading' => 'My Profile',
            'profile' => $profile,
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function update()
    {
        $userId = (int) session()->get('user_id');
        $users = new UserModel();
        $profile = $users->find($userId);
        if (!$profile || $profile['role'] !== 'teacher') {
            return redirect()->to('logout');
        }

        if (!$this->validate([
            'full_name' => 'required|min_length[2]|max_length[100]',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('teacher/profile');
        }

        $fullName = trim((string) $this->request->getPost('full_name'));
        if (mb_strlen($fullName) < 2) {
            session()->setFlashdata('error', 'Full name must contain at least 2 non-space characters.');
            return redirect()->to('teacher/profile');
        }

        if (!$users->update($userId, ['full_name' => $fullName])) {
            session()->setFlashdata('error', 'Could not update your profile. Please try again.');
            return redirect()->to('teacher/profile');
        }

        session()->set('full_name', $fullName);
        (new ActivityLogger())->log($userId, session()->get('username'), 'PROFILE_UPDATED', 'Updated teacher profile', 'INFO');
        session()->setFlashdata('saved', 'Profile updated.');
        return redirect()->to('teacher/profile');
    }

    public function changePassword()
    {
        $userId = (int) session()->get('user_id');
        $users = new UserModel();
        $profile = $users->find($userId);
        if (!$profile || $profile['role'] !== 'teacher') {
            return redirect()->to('logout');
        }

        $current = (string) $this->request->getPost('current_password');
        $new = (string) $this->request->getPost('new_password');
        $confirm = (string) $this->request->getPost('confirm_password');

        if (!password_verify($current, $profile['password'])) {
            session()->setFlashdata('error', 'Current password is incorrect.');
        } elseif (strlen($new) < 8) {
            session()->setFlashdata('error', 'New password must be at least 8 characters.');
        } elseif ($new !== $confirm) {
            session()->setFlashdata('error', 'New password and confirmation do not match.');
        } elseif (!$users->update($userId, ['password' => password_hash($new, PASSWORD_DEFAULT)])) {
            session()->setFlashdata('error', 'Could not change your password. Please try again.');
        } else {
            (new ActivityLogger())->log($userId, session()->get('username'), 'PASSWORD_CHANGED', null, 'INFO');
            session()->setFlashdata('saved', 'Password changed successfully.');
        }

        return redirect()->to('teacher/profile');
    }
}

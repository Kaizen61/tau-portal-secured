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
            session()->destroy();
            return redirect()->to('/');
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

    public function changePassword()
    {
        $userId = (int) session()->get('user_id');
        $users = new UserModel();
        $profile = $users->find($userId);
        if (!$profile || $profile['role'] !== 'teacher') {
            session()->destroy();
            return redirect()->to('/');
        }

        $current = (string) $this->request->getPost('current_password');
        $new = (string) $this->request->getPost('new_password');
        $confirm = (string) $this->request->getPost('confirm_password');

        if (!password_verify($current, $profile['password'])) {
            session()->setFlashdata('error', 'Current password is incorrect.');
        } elseif (mb_strlen($new) < 12) {
            session()->setFlashdata('error', 'New password must be at least 12 characters.');
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

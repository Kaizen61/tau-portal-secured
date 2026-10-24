<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    public function directory()
    {
        $admins = (new UserModel())
            ->where('role', 'admin')
            ->where('username !=', 'backup_admin')
            ->orderBy('full_name', 'ASC')
            ->findAll();

        return view('admin/admin_profiles', [
            'pageTitle' => 'Admin Profiles - TAU Student Portal',
            'activePage' => 'admins',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Admin Profiles',
            'admins' => $admins,
        ]);
    }

    public function index()
    {
        $profile = (new UserModel())->find(session()->get('user_id'));
        if (!$profile || $profile['role'] !== 'admin') {
            return redirect()->to('logout');
        }

        return view('admin/profile', [
            'pageTitle' => 'My Profile - TAU Student Portal',
            'activePage' => 'profile',
            'eyebrow' => 'Administration',
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
        if (!$profile || $profile['role'] !== 'admin') {
            return redirect()->to('logout');
        }

        if (!$this->validate([
            'full_name' => 'required|min_length[2]|max_length[100]',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('admin/profile');
        }

        $fullName = trim((string) $this->request->getPost('full_name'));
        if (mb_strlen($fullName) < 2) {
            session()->setFlashdata('error', 'Full name must contain at least 2 non-space characters.');
            return redirect()->to('admin/profile');
        }

        if (!$users->update($userId, ['full_name' => $fullName])) {
            session()->setFlashdata('error', 'Could not update your profile. Please try again.');
            return redirect()->to('admin/profile');
        }

        session()->set('full_name', $fullName);
        (new ActivityLogger())->log($userId, session()->get('username'), 'PROFILE_UPDATED', 'Updated admin profile', 'INFO');
        session()->setFlashdata('saved', 'Profile updated.');
        return redirect()->to('admin/profile');
    }

    public function changePassword()
    {
        $userId = (int) session()->get('user_id');
        $users = new UserModel();
        $profile = $users->find($userId);
        if (!$profile || $profile['role'] !== 'admin') {
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

        return redirect()->to('admin/profile');
    }
}

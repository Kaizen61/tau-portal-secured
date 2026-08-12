<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ActivityLogModel;
use App\Libraries\ActivityLogger;

class AuthController extends BaseController
{
    protected UserModel $users;
    protected ActivityLogModel $logs;
    protected ActivityLogger $activityLogger;

    // FIX (A09): honeytoken - no legitimate workflow ever logs in as this
    // account. Any attempt against it, successful or not, is a near-zero-
    // false-positive signal of an attacker probing the system.
    protected string $honeytokenUsername = 'backup_admin';

    // FIX (A09/A10): 5 CONSECUTIVE failed attempts locks the account for
    // a full hour (60 minutes) - enforced via UserModel::registerFailedLogin(),
    // stored persistently on the users row (failed_login_count/locked_until),
    // not a rolling time-window count that resets and lets attempts resume.
    protected int $lockoutThreshold = 5;
    protected int $lockoutDurationMinutes = 60;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->logs = new ActivityLogModel();
        $this->activityLogger = new ActivityLogger();
    }

    public function studentLogin()
    {
        return view('auth/student_login', ['error' => session()->getFlashdata('error')]);
    }

    public function staffLogin()
    {
        return view('auth/staff_login', ['error' => session()->getFlashdata('error')]);
    }

    public function processStudentLogin()
    {
        $studentNumber = trim($this->request->getPost('student_number') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if (!preg_match('/^\d{10}$/', $studentNumber)) {
            session()->setFlashdata('error', 'Please enter a valid 10-digit student number.');
            return redirect()->to('/');
        }
        $year = (int) substr($studentNumber, 0, 4);
        if ($year > (int) date('Y') || $year < 2000) {
            session()->setFlashdata('error', 'Please enter a valid 10-digit student number.');
            return redirect()->to('/');
        }

        return $this->attemptLogin($studentNumber, $password, 'student', '/');
    }

    public function processStaffLogin()
    {
        $username = trim($this->request->getPost('username') ?? '');
        $password = $this->request->getPost('password') ?? '';

        return $this->attemptLogin($username, $password, 'staff', 'staff-login');
    }

    protected function attemptLogin(string $identifier, string $password, string $mode, string $failRedirect)
    {
        if ($identifier === '' || $password === '') {
            session()->setFlashdata('error', 'Please fill in all fields.');
            return redirect()->to($failRedirect);
        }

        // FIX (A09): honeytoken check - the account itself being touched
        // is the signal, before we even look up whether the password is right.
        if ($mode === 'staff' && $identifier === $this->honeytokenUsername) {
            $this->activityLogger->log(null, $identifier, 'HONEYTOKEN_TRIGGERED',
                'Login attempted against the honeytoken account.', 'HIGH');
            session()->setFlashdata('error', 'Invalid username or password.');
            return redirect()->to($failRedirect);
        }

        $user = $mode === 'student'
            ? $this->users->findByStudentNumber($identifier)
            : $this->users->findByUsername($identifier);

        // FIX (A09/A10): if this account is currently locked out, block
        // immediately - checked BEFORE password_verify, so a correct
        // password does not bypass an active lockout.
        if ($user && $this->users->isLocked($user)) {
            $this->activityLogger->log($user['id'], $identifier, 'LOGIN_BLOCKED_LOCKOUT',
                "Blocked - account locked until {$user['locked_until']}.", 'WARNING');
            session()->setFlashdata('error', 'This account is temporarily locked due to repeated failed attempts. Please try again after 1 hour.');
            return redirect()->to($failRedirect);
        }

        // FIX (A10): generic message either way - never reveal whether the
        // identifier exists ("user not found" vs "wrong password").
        if (!$user || !password_verify($password, $user['password'])) {
            if ($user) {
                $justLocked = $this->users->registerFailedLogin($user['id'], $this->lockoutThreshold, $this->lockoutDurationMinutes);
                if ($justLocked) {
                    $this->activityLogger->log($user['id'], $identifier, 'SUSPICIOUS_ACTIVITY',
                        "Account locked for 1 hour after {$this->lockoutThreshold} consecutive failed login attempts.", 'HIGH');
                }
            }
            $this->activityLogger->log($user['id'] ?? null, $identifier, 'LOGIN_FAILED', null, 'INFO');
            session()->setFlashdata('error', $mode === 'student' ? 'Invalid student number or password.' : 'Invalid username or password.');
            return redirect()->to($failRedirect);
        }

        if ($mode === 'staff' && !in_array($user['role'], ['teacher', 'admin'], true)) {
            session()->setFlashdata('error', 'Invalid username or password.');
            return redirect()->to($failRedirect);
        }
        if ($mode === 'student' && $user['role'] !== 'student') {
            session()->setFlashdata('error', 'Invalid student number or password.');
            return redirect()->to($failRedirect);
        }

        // FIX: a successful login clears the failure streak - this is what
        // makes lockout based on CONSECUTIVE failures, not lifetime total.
        $this->users->clearFailedLogin($user['id']);

        // FIX: session fixation prevention.
        session()->regenerate(true);
        session()->set([
            'user_id' => $user['id'],
            'role' => $user['role'],
            'full_name' => $user['full_name'],
            'username' => $user['username'] ?? $user['student_number'],
        ]);

        $this->activityLogger->log($user['id'], $user['username'] ?? $user['student_number'], 'LOGIN_SUCCESS', null, 'INFO');

        return match ($user['role']) {
            'admin' => redirect()->to('admin/dashboard'),
            'teacher' => redirect()->to('teacher/dashboard'),
            default => redirect()->to('student/dashboard'),
        };
    }

    public function logout()
    {
        if (session()->get('user_id')) {
            $this->activityLogger->log(session()->get('user_id'), session()->get('username'), 'LOGOUT', null, 'INFO');
        }
        session()->destroy();
        return redirect()->to('/');
    }
}
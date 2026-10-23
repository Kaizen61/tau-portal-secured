<?php
// app/Config/Routes.php
// Merge these into your project's existing Routes.php (keep CI4's
// default $routes->get('/', ...) removed/replaced by the one below).

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// --- Public / Auth ---
$routes->get('/', 'AuthController::studentLogin');
$routes->get('staff-login', 'AuthController::staffLogin');
$routes->post('login/student', 'AuthController::processStudentLogin');
$routes->post('login/staff', 'AuthController::processStaffLogin');
$routes->get('logout', 'AuthController::logout');

// --- Admin (role filter: fail closed, only 'admin' role allowed) ---
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('users', 'Admin\UserController::index');
    $routes->post('users', 'Admin\UserController::create');
    $routes->get('students', 'Admin\StudentController::index');
    $routes->get('subjects', 'Admin\SubjectController::index');
    $routes->post('subjects', 'Admin\SubjectController::create');
    $routes->post('subjects/enroll', 'Admin\SubjectController::enroll');
    $routes->get('audit-trail', 'Admin\AuditController::index');
    $routes->get('audit-trail/poll', 'Admin\AuditController::poll');
    $routes->get('audit-trail/export', 'Admin\AuditController::export');
    $routes->get('dashboard/poll', 'Admin\DashboardController::poll');
    $routes->get('announcements', 'Admin\StaticController::announcements');
});

// --- Teacher ---
$routes->group('teacher', ['filter' => 'role:teacher'], function ($routes) {
    $routes->get('dashboard', 'Teacher\DashboardController::index');
    $routes->get('subjects/(:num)', 'Teacher\SubjectController::show/$1');
    $routes->get('subjects/(:num)/print', 'Teacher\SubjectController::printClassList/$1');
    $routes->post('subjects/(:num)/grade', 'Teacher\SubjectController::updateGrade/$1');
    $routes->get('grade-input', 'Teacher\GradeController::index');
    $routes->post('grade-input', 'Teacher\GradeController::store');
    $routes->get('attendance', 'Teacher\StaticController::attendance');
    $routes->get('assignments', 'Teacher\StaticController::assignments');
    $routes->get('schedule', 'Teacher\StaticController::schedule');
    $routes->get('announcements', 'Teacher\StaticController::announcements');
});

// --- Student ---
$routes->group('student', ['filter' => 'role:student'], function ($routes) {
    $routes->get('dashboard', 'Student\DashboardController::index');
    $routes->get('grades', 'Student\GradeController::index');
    $routes->get('grades/print', 'Student\GradeController::printReport');
    $routes->get('courses', 'Student\CourseController::index');
    $routes->get('courses/print', 'Student\CourseController::printCor');
    $routes->get('profile', 'Student\ProfileController::index');
    $routes->get('profile/print', 'Student\ProfileController::printRecord');
    $routes->post('profile', 'Student\ProfileController::update');
    $routes->post('profile/password', 'Student\ProfileController::changePassword');
    $routes->get('attendance', 'Student\StaticController::attendance');
    $routes->get('assignments', 'Student\StaticController::assignments');
    $routes->get('calendar', 'Student\StaticController::calendar');
    $routes->get('announcements', 'Student\StaticController::announcements');
});

$routes->match(['get', 'post'], 'login/staff', 'Login::staff');

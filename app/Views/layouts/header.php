<?php
// app/Views/layouts/header.php
// Expects: $pageTitle, $eyebrow, $pageHeading, $activePage
$role = session()->get('role');
$fullName = session()->get('full_name');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? 'TAU Student Portal') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/portal.css') ?>" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg width="46" height="46" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="47" fill="var(--uni-green-dark)" stroke="var(--uni-amber)" stroke-width="2"/>
                <circle cx="50" cy="50" r="39" fill="none" stroke="var(--uni-amber)" stroke-width="0.8" stroke-dasharray="1.6 2.2"/>
                <text x="50" y="49" text-anchor="middle" font-family="Merriweather, Georgia, serif" font-weight="700" font-size="19" fill="var(--uni-cream)">TAU</text>
                <text x="50" y="64" text-anchor="middle" font-family="Source Sans 3, sans-serif" font-size="5.5" letter-spacing="1.5" fill="var(--uni-amber-soft)">PORTAL</text>
            </svg>
            <div class="sidebar-brand-text">
                <strong>TAU Portal</strong>
                <span>Records System</span>
            </div>
        </div>

        <div class="sidebar-eyebrow"><?= $role === 'admin' ? 'Administration' : ($role === 'teacher' ? 'Faculty' : 'Student') ?></div>
        <nav class="sidebar-nav">
            <?php if ($role === 'admin'): ?>
                <a class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                <a class="<?= ($activePage ?? '') === 'students' ? 'active' : '' ?>" href="<?= site_url('admin/students') ?>">Students</a>
                <a class="<?= ($activePage ?? '') === 'users' ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>">Teachers</a>
                <a class="<?= ($activePage ?? '') === 'subjects' ? 'active' : '' ?>" href="<?= site_url('admin/subjects') ?>">Manage Subjects</a>
                <a class="<?= ($activePage ?? '') === 'add-account' ? 'active' : '' ?>" href="<?= site_url('admin/add-account') ?>">Add Account</a>
                <a class="<?= ($activePage ?? '') === 'announcements' ? 'active' : '' ?>" href="<?= site_url('admin/announcements') ?>">Announcements</a>
                <a class="<?= ($activePage ?? '') === 'audit' ? 'active' : '' ?>" href="<?= site_url('admin/audit-trail') ?>">System Activity Logs</a>
                <a class="<?= ($activePage ?? '') === 'admins' ? 'active' : '' ?>" href="<?= site_url('admin/admins') ?>">Admin Profiles</a>
                <a class="<?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>" href="<?= site_url('admin/profile') ?>">My Profile</a>
            <?php elseif ($role === 'teacher'): ?>
                <a class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('teacher/dashboard') ?>">Dashboard</a>
                <a class="<?= ($activePage ?? '') === 'subjects' ? 'active' : '' ?>" href="<?= site_url('teacher/dashboard') ?>">My Subjects</a>
                <a class="<?= ($activePage ?? '') === 'announcements' ? 'active' : '' ?>" href="<?= site_url('teacher/announcements') ?>">Announcements</a>
                <a class="<?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>" href="<?= site_url('teacher/profile') ?>">My Profile</a>
            <?php elseif ($role === 'student'): ?>
                <a class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('student/dashboard') ?>">Dashboard</a>
                <a class="<?= ($activePage ?? '') === 'courses' ? 'active' : '' ?>" href="<?= site_url('student/courses') ?>">Courses</a>
                <a class="<?= ($activePage ?? '') === 'grades' ? 'active' : '' ?>" href="<?= site_url('student/grades') ?>">Grades</a>
                <a class="<?= ($activePage ?? '') === 'announcements' ? 'active' : '' ?>" href="<?= site_url('student/announcements') ?>">Announcements</a>
                <a class="<?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>" href="<?= site_url('student/profile') ?>">My Profile</a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <?= esc($fullName) ?>
                <span class="badge badge-role-<?= esc($role) ?>"><?= esc(ucfirst($role)) ?></span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-light w-100" data-bs-toggle="modal" data-bs-target="#logoutConfirmationModal">Logout</button>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <?php if (!empty($eyebrow)): ?><div class="eyebrow"><?= esc($eyebrow) ?></div><?php endif; ?>
            <h1 class="topbar-title"><?= esc($pageHeading ?? $pageTitle ?? '') ?></h1>
        </header>
        <main class="content-area">

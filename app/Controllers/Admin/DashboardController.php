<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SubjectModel;
use App\Models\GradeModel;
use App\Models\ActivityLogModel;
use App\Models\AnnouncementModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $users = new UserModel();
        $subjects = new SubjectModel();
        $grades = new GradeModel();
        $logs = new ActivityLogModel();
        $db = db_connect();
        $collegeLabels = [
            'College of Veterinary Medicine (CVM)',
            'College of Engineering and Technology (CET)',
            'College of Education (CEd)',
            'College of Business and Management (CBM)',
            'College of Arts and Sciences (CAS)',
            'College of Agriculture and Forestry (CAF)',
        ];

        $collegeTotals = $db->table('users')
            ->select("COALESCE(NULLIF(college, ''), 'Unassigned') AS label, COUNT(*) AS total", false)
            ->where('role', 'student');
        if ($users->hasActiveStatusColumn()) {
            $collegeTotals->where('is_active', 1);
        }
        $collegeTotals = $collegeTotals
            ->groupBy("COALESCE(NULLIF(college, ''), 'Unassigned')", false)
            ->get()
            ->getResultArray();
        $studentDistribution = array_fill_keys($collegeLabels, 0);
        $unassignedTotal = 0;
        foreach ($collegeTotals as $collegeTotal) {
            $label = (string) $collegeTotal['label'];
            if (array_key_exists($label, $studentDistribution)) {
                $studentDistribution[$label] = (int) $collegeTotal['total'];
            } else {
                $unassignedTotal += (int) $collegeTotal['total'];
            }
        }
        $studentDistribution = array_map(
            static fn (string $label, int $total): array => ['label' => $label, 'total' => $total],
            array_keys($studentDistribution),
            array_values($studentDistribution)
        );
        if ($unassignedTotal > 0) {
            $studentDistribution[] = ['label' => 'Unassigned / Other', 'total' => $unassignedTotal];
        }

        $totalGrades = $grades->countAllResults();
        $pendingGrades = (new GradeModel())->where('grade', null)->countAllResults();

        $data = [
            'pageTitle' => 'Admin Dashboard - TAU Student Portal',
            'activePage' => 'dashboard',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Dashboard Overview',
            'studentCount' => $users->where('role', 'student')->whereActive()->countAllResults(),
            'teacherCount' => $users->where('role', 'teacher')->whereActive()->countAllResults(),
            'subjectCount' => $subjects->countAllResults(),
            'pendingCount' => $pendingGrades,
            'highSeverityCount' => $logs->recentHighSeverityCount(),
            'gradedCount' => $totalGrades - $pendingGrades,
            'studentDistribution' => $studentDistribution,
            'collegeLabels' => $collegeLabels,
            'recentAnnouncements' => (new AnnouncementModel())
                ->orderBy('created_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->findAll(4),
            'recentActivity' => $logs->select('action, username, severity, details, created_at')
                ->orderBy('created_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->findAll(6),
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Refreshes live statistics and high-severity alerts without a page reload.
     */
    public function poll()
    {
        $users = new UserModel();
        $subjects = new SubjectModel();
        $grades = new GradeModel();
        $logs = new ActivityLogModel();
        $totalGrades = $grades->countAllResults();
        $pendingGrades = (new GradeModel())->where('grade', null)->countAllResults();

        return $this->response->setJSON([
            'studentCount' => $users->where('role', 'student')->whereActive()->countAllResults(),
            'teacherCount' => $users->where('role', 'teacher')->whereActive()->countAllResults(),
            'subjectCount' => $subjects->countAllResults(),
            'pendingCount' => $pendingGrades,
            'gradedCount' => $totalGrades - $pendingGrades,
            'highSeverityCount' => $logs->recentHighSeverityCount(),
        ]);
    }
}
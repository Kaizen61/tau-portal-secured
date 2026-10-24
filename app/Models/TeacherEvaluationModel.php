<?php

namespace App\Models;

use App\Libraries\AcademicProgram;
use App\Libraries\SchoolYear;
use CodeIgniter\Model;

class TeacherEvaluationModel extends Model
{
    protected $table = 'teacher_evaluations';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'student_id', 'teacher_id', 'school_year', 'semester', 'submitted_at',
        'strengths', 'improvements', 'additional_comments',
    ];

    public function hasSubmitted(int $studentId, int $teacherId, string $semester): bool
    {
        return $this->where('student_id', $studentId)
            ->where('teacher_id', $teacherId)
            ->where('school_year', SchoolYear::current())
            ->where('semester', $semester)
            ->countAllResults() > 0;
    }

    public function eligibleTeachersForStudent(int $studentId, string $semester): array
    {
        $rows = $this->db->table('grades g')
            ->select('t.id AS teacher_id, t.full_name AS teacher_name, t.employee_id, t.college, s.id AS subject_id, s.subject_code, s.subject_name, s.semester')
            ->join('subjects s', 's.id = g.subject_id')
            ->join('users t', 't.id = s.teacher_id AND t.role = \'teacher\'')
            ->where('g.student_id', $studentId)
            ->where('g.school_year', SchoolYear::current())
            ->where('s.semester', $semester)
            ->groupBy('t.id, t.full_name, t.employee_id, t.college, s.id, s.subject_code, s.subject_name')
            ->orderBy('t.full_name')
            ->orderBy('s.subject_code')
            ->get()
            ->getResultArray();

        $teachers = [];
        foreach ($rows as $row) {
            $teacherId = (int) $row['teacher_id'];
            if (!isset($teachers[$teacherId])) {
                $teachers[$teacherId] = [
                    'teacher_id' => $teacherId,
                    'teacher_name' => $row['teacher_name'],
                    'employee_id' => $row['employee_id'],
                    'college' => $row['college'],
                    'subjects' => [],
                ];
            }
            $teachers[$teacherId]['subjects'][] = [
                'subject_code' => $row['subject_code'],
                'subject_name' => $row['subject_name'],
                'semester' => $row['semester'],
            ];
        }

        $submitted = [];
        foreach ($this->db->table('teacher_evaluations')
            ->select('teacher_id')
            ->where('student_id', $studentId)
            ->where('school_year', SchoolYear::current())
            ->where('semester', $semester)
            ->get()
            ->getResultArray() as $row) {
            $submitted[(int) $row['teacher_id']] = true;
        }

        foreach ($teachers as &$teacher) {
            $teacher['submitted'] = isset($submitted[$teacher['teacher_id']]);
        }
        unset($teacher);

        return array_values($teachers);
    }

    public function evaluationsForStudent(int $studentId, string $semester): array
    {
        return $this->scoresByEvaluation(
            $this->db->table('teacher_evaluations e')
                ->select('e.id, e.teacher_id, e.school_year, e.semester, e.submitted_at, e.strengths, e.improvements, e.additional_comments, t.full_name AS teacher_name')
                ->join('users t', 't.id = e.teacher_id')
                ->where('e.student_id', $studentId)
                ->where('e.school_year', SchoolYear::current())
                ->where('e.semester', $semester)
                ->orderBy('t.full_name')
                ->get()
                ->getResultArray()
        );
    }

    public function adminReport(array $filters = []): array
    {
        $filters = array_replace(['q' => '', 'college' => '', 'semester' => 'all', 'teacher_id' => 0], $filters);
        $teacherQuery = $this->db->table('users')
            ->select('id AS teacher_id, full_name AS teacher_name, employee_id, college')
            ->where('role', 'teacher')
            ->where('username !=', 'backup_admin');
        if ($filters['teacher_id'] > 0) {
            $teacherQuery->where('id', $filters['teacher_id']);
        }
        if ($filters['q'] !== '') {
            $teacherQuery
                ->groupStart()
                    ->like('full_name', $filters['q'])
                    ->orLike('employee_id', $filters['q'])
                ->groupEnd();
        }
        if ($filters['college'] !== '') {
            if ($filters['college'] === 'College of Agriculture and Forestry (CAF)') {
                $teacherQuery->whereIn('college', self::AGRICULTURE_COLLEGE_NAMES);
            } else {
                $teacherQuery->where('college', $filters['college']);
            }
        }
        $teachers = $teacherQuery->orderBy('full_name')->get()->getResultArray();

        if (!$teachers) {
            return [];
        }
        foreach ($teachers as &$teacher) {
            $teacher['college'] = self::normalizeCollege($teacher['college']);
        }
        unset($teacher);
        $teacherIds = array_column($teachers, 'teacher_id');

        $evaluationQuery = $this->db->table('teacher_evaluations e')
                ->select('e.id, e.student_id, e.teacher_id, e.school_year, e.semester, e.submitted_at, e.strengths, e.improvements, e.additional_comments, s.full_name AS student_name, s.student_number')
                ->join('users s', 's.id = e.student_id')
                ->where('e.school_year', SchoolYear::current())
                ->whereIn('e.teacher_id', $teacherIds);
        if (in_array($filters['semester'], ['1st', '2nd'], true)) {
            $evaluationQuery->where('e.semester', $filters['semester']);
        }
        $evaluations = $this->scoresByEvaluation(
            $evaluationQuery->orderBy('e.submitted_at', 'DESC')->get()->getResultArray()
        );

        $evaluationsByTeacher = [];
        foreach ($evaluations as $evaluation) {
            $evaluationsByTeacher[(int) $evaluation['teacher_id']][] = $evaluation;
        }

        foreach ($teachers as &$teacher) {
            $teacherId = (int) $teacher['teacher_id'];
            $teacher['evaluations'] = $evaluationsByTeacher[$teacherId] ?? [];
            $teacher['evaluation_count'] = count($teacher['evaluations']);
            $teacher['total_points'] = 0;
            $teacher['semester_summaries'] = [];
            $semesters = in_array($filters['semester'], ['1st', '2nd'], true)
                ? [$filters['semester']]
                : ['1st', '2nd'];
            foreach ($semesters as $semester) {
                $teacher['semester_summaries'][$semester] = [
                    'evaluation_count' => 0,
                    'total_points' => 0,
                    'average_score' => null,
                ];
            }
            foreach ($teacher['evaluations'] as $evaluation) {
                $evaluationPoints = array_sum($evaluation['scores']);
                $teacher['total_points'] += $evaluationPoints;
                $semester = $evaluation['semester'];
                $teacher['semester_summaries'][$semester]['evaluation_count']++;
                $teacher['semester_summaries'][$semester]['total_points'] += $evaluationPoints;
            }
            $scoreCount = $teacher['evaluation_count'] * count(\App\Libraries\TeacherEvaluationCriteria::CRITERIA);
            $teacher['average_score'] = $scoreCount > 0
                ? round($teacher['total_points'] / $scoreCount, 2)
                : null;
            foreach ($teacher['semester_summaries'] as &$summary) {
                $semesterScoreCount = $summary['evaluation_count'] * count(\App\Libraries\TeacherEvaluationCriteria::CRITERIA);
                $summary['average_score'] = $semesterScoreCount > 0
                    ? round($summary['total_points'] / $semesterScoreCount, 2)
                    : null;
            }
            unset($summary);
        }
        unset($teacher);

        return $teachers;
    }

    private const AGRICULTURE_COLLEGE_NAMES = [
        'College of Agriculture and Forestry (CAF)',
        'College of Agriculture and Forestry',
        'College of Agriculture and Foresty',
        'College of Agriculture',
    ];

    private static function normalizeCollege(?string $college): ?string
    {
        if ($college !== null && in_array(strtolower(trim($college)), array_map('strtolower', self::AGRICULTURE_COLLEGE_NAMES), true)) {
            return 'College of Agriculture and Forestry (CAF)';
        }

        return $college;
    }

    private function scoresByEvaluation(array $evaluations): array
    {
        if (!$evaluations) {
            return [];
        }

        $ids = array_column($evaluations, 'id');
        $scoreRows = $this->db->table('teacher_evaluation_scores')
            ->whereIn('evaluation_id', $ids)
            ->get()
            ->getResultArray();
        $scoresById = [];
        foreach ($scoreRows as $row) {
            $scoresById[(int) $row['evaluation_id']][$row['criterion']] = (int) $row['score'];
        }

        foreach ($evaluations as &$evaluation) {
            $evaluation['scores'] = $scoresById[(int) $evaluation['id']] ?? [];
        }
        unset($evaluation);

        return $evaluations;
    }
}

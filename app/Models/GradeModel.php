<?php

namespace App\Models;

use CodeIgniter\Model;

class GradeModel extends Model
{
    protected $table = 'grades';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    // Explicit controller-level validation is used instead (see
    // Controllers) so partial updates (e.g. updating only 'grade')
    // don't fail against rules meant for full-record inserts.
    protected $skipValidation = true;
    protected $allowedFields = ['student_id', 'subject_id', 'school_year', 'grade'];

    // FIX (A10): grade must be numeric and within a real range - not a
    // free-text field that accepts anything, unlike the vulnerable version.
    protected $validationRules = [
        'student_id' => 'required|is_natural_no_zero',
        'subject_id' => 'required|is_natural_no_zero',
        'school_year' => 'required|regex_match[/^\d{4}-\d{4}$/]',
        'grade' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
    ];

    public function enrollmentExists(int $studentId, int $subjectId, string $schoolYear): bool
    {
        return $this->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('school_year', $schoolYear)
            ->first() !== null;
    }

    /**
     * Returns grade rows for a student joined with subject info,
     * grouped by school_year + semester, each group tagged with its
     * computed GWA (units-weighted average of the graded subjects only).
     */
    public function getGradesGroupedBySemester(int $studentId, ?string $schoolYear = null, ?string $semester = null): array
    {
        $builder = $this->db->table('grades g')
            ->select('g.id, g.school_year, g.grade, s.subject_code, s.subject_name, s.units, s.year_level, s.semester')
            ->join('subjects s', 's.id = g.subject_id')
            ->where('g.student_id', $studentId)
            ->orderBy('g.school_year', 'DESC')
            ->orderBy('s.semester', 'ASC')
            ->orderBy('s.subject_code', 'ASC');

        if ($schoolYear) $builder->where('g.school_year', $schoolYear);
        if ($semester) $builder->where('s.semester', $semester);

        $rows = $builder->get()->getResultArray();

        $groups = [];
        foreach ($rows as $row) {
            $key = $row['school_year'] . ' | ' . $row['semester'];
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'school_year' => $row['school_year'],
                    'semester' => $row['semester'],
                    'year_level' => $row['year_level'],
                    'subjects' => [],
                    'total_weighted' => 0,
                    'total_units' => 0,
                ];
            }
            $groups[$key]['subjects'][] = $row;
            if ($row['grade'] !== null) {
                $groups[$key]['total_weighted'] += ((float) $row['grade']) * (int) $row['units'];
                $groups[$key]['total_units'] += (int) $row['units'];
            }
        }

        // FIX: GWA is the units-weighted average of graded subjects only
        // (ungraded/pending subjects are excluded, not treated as zero).
        foreach ($groups as $key => $g) {
            $groups[$key]['gwa'] = $g['total_units'] > 0
                ? round($g['total_weighted'] / $g['total_units'], 2)
                : null;
        }

        return $groups;
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table = 'subjects';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    // Explicit controller-level validation is used instead (see
    // Controllers) so partial updates (e.g. updating only 'grade')
    // don't fail against rules meant for full-record inserts.
    protected $skipValidation = true;
    protected $allowedFields = ['subject_code', 'subject_name', 'units', 'year_level', 'semester', 'teacher_id'];

    protected $validationRules = [
        'subject_code' => 'required',
        'subject_name' => 'required',
        'units' => 'required|is_natural_no_zero',
        'year_level' => 'required|in_list[1,2,3,4]',
        'semester' => 'required|in_list[1st,2nd]',
    ];

    public function forTeacher(int $teacherId, ?string $yearLevel = null, ?string $semester = null)
    {
        $builder = $this->where('teacher_id', $teacherId);
        if ($yearLevel) $builder->where('year_level', $yearLevel);
        if ($semester) $builder->where('semester', $semester);
        return $builder->orderBy('year_level')->orderBy('semester')->orderBy('subject_code')->findAll();
    }
}

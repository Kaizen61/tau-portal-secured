<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\AcademicProgram;
use App\Libraries\SchoolYear;
use App\Libraries\TeacherEvaluationCriteria;
use App\Models\TeacherEvaluationModel;

class TeacherEvaluationController extends BaseController
{
    public function index()
    {
        $data = $this->reportData();
        return view('admin/teacher_evaluations', $data);
    }

    public function print()
    {
        $data = $this->reportData();
        $data['printTitle'] = 'Teacher Evaluation Report - TAU Student Portal';
        $data['documentTitle'] = 'Teacher Evaluation Report';
        $data['printOrientation'] = 'landscape';
        $data['printedAt'] = date('F j, Y g:i A');
        $data['autoPrint'] = true;

        return view('admin/print_teacher_evaluations', $data);
    }

    private function reportData(): array
    {
        $model = new TeacherEvaluationModel();
        $colleges = array_keys(AcademicProgram::PROGRAMS_BY_COLLEGE);
        $q = trim((string) $this->request->getGet('q'));
        $q = mb_substr($q, 0, 100);
        $college = (string) $this->request->getGet('college');
        $college = in_array($college, $colleges, true) ? $college : '';
        $semester = (string) $this->request->getGet('semester');
        $semester = in_array($semester, ['1st', '2nd'], true) ? $semester : 'all';
        $teacherId = filter_var($this->request->getGet('teacher_id'), FILTER_VALIDATE_INT);
        $teacherId = $teacherId !== false && $teacherId > 0 ? $teacherId : 0;
        $filters = ['q' => $q, 'college' => $college, 'semester' => $semester, 'teacher_id' => $teacherId];

        return [
            'pageTitle' => 'Teacher Evaluations - TAU Student Portal',
            'activePage' => 'evaluations',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Teacher Evaluations',
            'schoolYear' => SchoolYear::current(),
            'criteria' => TeacherEvaluationCriteria::CRITERIA,
            'criteriaSections' => TeacherEvaluationCriteria::SECTIONS,
            'commentFields' => TeacherEvaluationCriteria::COMMENT_FIELDS,
            'teachers' => $model->adminReport($filters),
            'colleges' => $colleges,
            'filters' => $filters,
        ];
    }
}

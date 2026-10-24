<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\SchoolYear;
use App\Libraries\TeacherEvaluationCriteria;
use App\Models\TeacherEvaluationModel;

class TeacherEvaluationController extends BaseController
{
    public function index()
    {
        return view('admin/teacher_evaluations', [
            'pageTitle' => 'Teacher Evaluations - TAU Student Portal',
            'activePage' => 'evaluations',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Teacher Evaluations',
            'schoolYear' => SchoolYear::current(),
            'criteria' => TeacherEvaluationCriteria::CRITERIA,
            'commentFields' => TeacherEvaluationCriteria::COMMENT_FIELDS,
            'teachers' => (new TeacherEvaluationModel())->adminReport(),
        ]);
    }
}

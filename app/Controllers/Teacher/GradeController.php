<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\UserModel;
use App\Models\GradeModel;
use App\Libraries\ActivityLogger;

class GradeController extends BaseController
{
    public function index()
    {
        $subjects = new SubjectModel();
        $users = new UserModel();
        $teacherId = session()->get('user_id');

        $data = [
            'pageTitle' => 'Input Grade - TAU Student Portal',
            'activePage' => 'subjects',
            'eyebrow' => 'Faculty',
            'pageHeading' => 'Input a Grade Record',
            'subjects' => $subjects->where('teacher_id', $teacherId)->findAll(),
            'students' => $users->where('role', 'student')->orderBy('full_name')->findAll(),
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ];
        return view('teacher/grade_input', $data);
    }

    public function store()
    {
        $subjects = new SubjectModel();
        $grades = new GradeModel();
        $teacherId = session()->get('user_id');

        $rules = [
            'student_id' => 'required|is_natural_no_zero',
            'subject_id' => 'required|is_natural_no_zero',
            'school_year' => 'required|regex_match[/^\d{4}-\d{4}$/]',
            'grade' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];
        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('teacher/grade-input');
        }

        $subjectId = $this->request->getPost('subject_id');

        // FIX (A01/IDOR): confirm this subject belongs to the logged-in teacher.
        if (!$subjects->where('id', $subjectId)->where('teacher_id', $teacherId)->first()) {
            session()->setFlashdata('error', 'You are not assigned to that subject.');
            return redirect()->to('teacher/grade-input');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $id = $grades->insert([
            'student_id' => $this->request->getPost('student_id'),
            'subject_id' => $subjectId,
            'school_year' => $this->request->getPost('school_year'),
            'grade' => $this->request->getPost('grade'),
        ]);

        (new ActivityLogger())->log($teacherId, session()->get('username'), 'GRADE_CREATED',
            "Created grade record #$id for subject #$subjectId", 'INFO');

        $db->transComplete();

        session()->setFlashdata('saved', $db->transStatus() ? 'Grade saved successfully.' : 'Could not save the grade (duplicate enrollment?).');
        return redirect()->to('teacher/grade-input');
    }
}

<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\UserModel;
use App\Models\GradeModel;
use App\Libraries\ActivityLogger;
use App\Libraries\SchoolYear;
use CodeIgniter\Database\Exceptions\DatabaseException;

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
            'students' => $users->where('role', 'student')->whereActive()->orderBy('full_name')->findAll(),
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
            'schoolYears' => SchoolYear::available(),
            'currentSchoolYear' => SchoolYear::current(),
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
            'school_year' => 'required',
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

        $studentUsers = new UserModel();
        if (!$studentUsers->where('id', $this->request->getPost('student_id'))
            ->where('role', 'student')->whereActive()->first()) {
            session()->setFlashdata('error', 'Select an active student for the grade record.');
            return redirect()->to('teacher/grade-input');
        }

        $studentId = (int) $this->request->getPost('student_id');
        $schoolYear = trim((string) $this->request->getPost('school_year'));
        if (!SchoolYear::isAvailable($schoolYear)) {
            session()->setFlashdata('error', 'Select a valid school year.');
            return redirect()->to('teacher/grade-input');
        }
        if ($grades->enrollmentExists($studentId, (int) $subjectId, $schoolYear)) {
            session()->setFlashdata('error', 'This student already has a record for that subject and school year.');
            return redirect()->to('teacher/grade-input');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $id = $grades->insert([
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'school_year' => $schoolYear,
                'grade' => $this->request->getPost('grade'),
            ]);
        } catch (DatabaseException) {
            $db->transRollback();
            session()->setFlashdata('error', 'This student already has a record for that subject and school year, or the grade could not be saved.');
            return redirect()->to('teacher/grade-input');
        }
        if (!$id || !$db->transStatus()) {
            $db->transRollback();
            session()->setFlashdata('error', 'This student already has a record for that subject and school year, or the grade could not be saved.');
            return redirect()->to('teacher/grade-input');
        }

        (new ActivityLogger())->log($teacherId, session()->get('username'), 'GRADE_CREATED',
            "Created grade record #$id for subject #$subjectId", 'INFO');

        $db->transCommit();

        session()->setFlashdata('saved', 'Grade saved successfully.');
        return redirect()->to('teacher/grade-input');
    }
}

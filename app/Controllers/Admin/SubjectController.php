<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\UserModel;
use App\Models\GradeModel;
use App\Libraries\ActivityLogger;

class SubjectController extends BaseController
{
    public function index()
    {
        $subjects = new SubjectModel();
        $users = new UserModel();

        $yearLevel = $this->request->getGet('year_level');
        $semester = $this->request->getGet('semester');

        $builder = $subjects->select('subjects.*, users.full_name as teacher_name')
            ->join('users', 'users.id = subjects.teacher_id', 'left');
        if (in_array($yearLevel, ['1','2','3','4'], true)) $builder->where('subjects.year_level', $yearLevel);
        if (in_array($semester, ['1st','2nd'], true)) $builder->where('subjects.semester', $semester);

        $data = [
            'pageTitle' => 'Manage Subjects - TAU Student Portal',
            'activePage' => 'subjects',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Manage Subjects',
            'subjectList' => $builder->orderBy('subjects.year_level')->orderBy('subjects.semester')->orderBy('subjects.subject_code')->findAll(),
            'teachers' => $users->where('role', 'teacher')->findAll(),
            'students' => $users->where('role', 'student')->findAll(),
            'allSubjects' => $subjects->orderBy('year_level')->orderBy('semester')->orderBy('subject_code')->findAll(),
            'filterYear' => $yearLevel,
            'filterSem' => $semester,
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ];
        return view('admin/subjects', $data);
    }

    public function create()
    {
        $subjects = new SubjectModel();

        $rules = [
            'subject_code' => 'required',
            'subject_name' => 'required',
            'units' => 'required|is_natural_no_zero',
            'year_level' => 'required|in_list[1,2,3,4]',
            'semester' => 'required|in_list[1st,2nd]',
            'teacher_id' => 'required|is_natural_no_zero',
        ];
        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('admin/subjects');
        }

        $id = $subjects->insert([
            'subject_code' => $this->request->getPost('subject_code'),
            'subject_name' => $this->request->getPost('subject_name'),
            'units' => $this->request->getPost('units'),
            'year_level' => $this->request->getPost('year_level'),
            'semester' => $this->request->getPost('semester'),
            'teacher_id' => $this->request->getPost('teacher_id'),
        ]);

        (new ActivityLogger())->log(session()->get('user_id'), session()->get('username'), 'SUBJECT_CREATED',
            "Created subject #$id", 'INFO');

        session()->setFlashdata('saved', 'Subject added.');
        return redirect()->to('admin/subjects');
    }

    public function enroll()
    {
        $grades = new GradeModel();

        $rules = [
            'student_id' => 'required|is_natural_no_zero',
            'subject_id' => 'required|is_natural_no_zero',
            'school_year' => 'required|regex_match[/^\d{4}-\d{4}$/]',
        ];
        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('admin/subjects');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $grades->insert([
            'student_id' => $this->request->getPost('student_id'),
            'subject_id' => $this->request->getPost('subject_id'),
            'school_year' => $this->request->getPost('school_year'),
            'grade' => null,
        ]);

        (new ActivityLogger())->log(session()->get('user_id'), session()->get('username'), 'STUDENT_ENROLLED',
            "Enrolled student #{$this->request->getPost('student_id')} into subject #{$this->request->getPost('subject_id')}", 'INFO');

        $db->transComplete();

        session()->setFlashdata('saved', $db->transStatus() ? 'Student enrolled.' : 'Could not enroll the student (already enrolled?).');
        return redirect()->to('admin/subjects');
    }
}

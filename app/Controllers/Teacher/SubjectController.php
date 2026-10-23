<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\GradeModel;
use App\Libraries\ActivityLogger;

class SubjectController extends BaseController
{
    public function show($subjectId = null)
    {
        $subjects = new SubjectModel();
        $grades = new GradeModel();
        $teacherId = session()->get('user_id');

        if (!$subjectId || !is_numeric($subjectId)) {
            return $this->fail('No subject was specified.');
        }

        // FIX (A01/IDOR): ownership check - a teacher can only view a
        // subject actually assigned to them, no matter the URL.
        $subject = $subjects->where('id', $subjectId)->where('teacher_id', $teacherId)->first();

        if (!$subject) {
            (new ActivityLogger())->log($teacherId, session()->get('username'), 'ACCESS_DENIED',
                "Attempted to view subject #$subjectId, not assigned to them.", 'WARNING');
            return $this->fail('That subject was not found, or is not assigned to you.', 404);
        }

        $students = $grades->select('grades.id as grade_row_id, users.full_name, users.year_level, grades.school_year, grades.grade')
            ->join('users', 'users.id = grades.student_id')
            ->where('subject_id', $subjectId)
            ->orderBy('grades.school_year', 'DESC')->orderBy('users.full_name')
            ->findAll();

        $data = [
            'pageTitle' => esc($subject['subject_name']) . ' - TAU Student Portal',
            'activePage' => 'subjects',
            'eyebrow' => 'Faculty · ' . $subject['subject_code'],
            'pageHeading' => $subject['subject_name'],
            'subject' => $subject,
            'students' => $students,
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ];
        return view('teacher/subject_students', $data);
    }

    public function printClassList($subjectId = null)
    {
        $subjects = new SubjectModel();
        $grades = new GradeModel();
        $teacherId = session()->get('user_id');

        if (!$subjectId || !is_numeric($subjectId)) {
            return $this->fail('No subject was specified.');
        }

        $subject = $subjects->where('id', $subjectId)->where('teacher_id', $teacherId)->first();
        if (!$subject) {
            (new ActivityLogger())->log($teacherId, session()->get('username'), 'ACCESS_DENIED',
                "Attempted to print class list for subject #$subjectId, not assigned to them.", 'WARNING');
            return $this->fail('That subject was not found, or is not assigned to you.', 404);
        }

        $students = $grades->select('users.full_name, users.student_number, users.year_level, grades.school_year, grades.grade')
            ->join('users', 'users.id = grades.student_id')
            ->where('subject_id', $subjectId)
            ->orderBy('users.full_name')
            ->findAll();

        return view('teacher/print_class_list', [
            'printTitle' => 'Class List - ' . $subject['subject_code'],
            'documentTitle' => 'Class List / Grade Sheet',
            'printedAt' => date('F j, Y g:i A'),
            'autoPrint' => false,
            'subject' => $subject,
            'students' => $students,
            'facultyName' => session()->get('full_name'),
        ]);
    }

    public function updateGrade($subjectId = null)
    {
        $subjects = new SubjectModel();
        $grades = new GradeModel();
        $teacherId = session()->get('user_id');

        $subject = $subjects->where('id', $subjectId)->where('teacher_id', $teacherId)->first();
        if (!$subject) {
            return $this->fail('That subject was not found, or is not assigned to you.', 404);
        }

        $rules = [
            'grade_row_id' => 'required|is_natural_no_zero',
            'grade' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];
        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('teacher/subjects/' . $subjectId);
        }

        $gradeRowId = $this->request->getPost('grade_row_id');
        $newGrade = $this->request->getPost('grade');

        // FIX (A01/IDOR): confirm this grade row actually belongs to THIS subject.
        $existing = $grades->where('id', $gradeRowId)->where('subject_id', $subjectId)->first();
        if (!$existing) {
            session()->setFlashdata('error', 'That grade record does not belong to this subject.');
            return redirect()->to('teacher/subjects/' . $subjectId);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $grades->update($gradeRowId, ['grade' => $newGrade]);

        $oldDisplay = $existing['grade'] === null ? 'ungraded' : $existing['grade'];
        (new ActivityLogger())->log($teacherId, session()->get('username'), 'GRADE_UPDATED',
            "Grade record #$gradeRowId in subject #$subjectId changed from '$oldDisplay' to '$newGrade'", 'INFO');

        $db->transComplete();

        session()->setFlashdata('saved', $db->transStatus() ? 'Grade updated.' : 'Could not save the grade.');
        return redirect()->to('teacher/subjects/' . $subjectId);
    }

    protected function fail(string $message, int $code = 400)
    {
        return $this->response->setStatusCode($code)->setBody(
            view('errors_custom/generic', ['message' => $message])
        );
    }
}

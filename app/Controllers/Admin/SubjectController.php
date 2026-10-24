<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\UserModel;
use App\Models\GradeModel;
use App\Libraries\ActivityLogger;
use App\Libraries\SchoolYear;

class SubjectController extends BaseController
{
    public function index()
    {
        $subjects = new SubjectModel();
        $users = new UserModel();

        $yearLevel = $this->request->getGet('year_level');
        $semester = $this->request->getGet('semester');
        $q = trim((string) $this->request->getGet('q'));

        $builder = $subjects->select('subjects.*, users.full_name as teacher_name')
            ->join('users', 'users.id = subjects.teacher_id', 'left');
        if (in_array($yearLevel, ['1','2','3','4'], true)) $builder->where('subjects.year_level', $yearLevel);
        if (in_array($semester, ['1st','2nd'], true)) $builder->where('subjects.semester', $semester);
        if ($q !== '') {
            $builder->groupStart()
                ->like('subjects.subject_code', $q)
                ->orLike('subjects.subject_name', $q)
                ->orLike('users.full_name', $q)
                ->groupEnd();
        }

        $data = [
            'pageTitle' => 'Manage Subjects - TAU Student Portal',
            'activePage' => 'subjects',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Manage Subjects',
            'subjectList' => $builder->orderBy('subjects.year_level')->orderBy('subjects.semester')->orderBy('subjects.subject_code')->findAll(),
            'teachers' => $users->where('role', 'teacher')->whereActive()->findAll(),
            'students' => $users->where('role', 'student')->whereActive()->findAll(),
            'allSubjects' => $subjects->orderBy('year_level')->orderBy('semester')->orderBy('subject_code')->findAll(),
            'filterYear' => $yearLevel,
            'filterSem' => $semester,
            'q' => $q,
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
            'schoolYears' => SchoolYear::available(),
            'currentSchoolYear' => SchoolYear::current(),
        ];
        return view('admin/subjects', $data);
    }

    public function edit(int $id)
    {
        $subjects = new SubjectModel();
        $subject = $subjects->find($id);
        if (!$subject) {
            session()->setFlashdata('error', 'Subject not found.');
            return redirect()->to('admin/subjects');
        }

        $users = new UserModel();
        $teachers = $users->where('role', 'teacher')->whereActive()->findAll();
        if (!empty($subject['teacher_id']) && !array_filter($teachers, static fn (array $teacher): bool => (int) $teacher['id'] === (int) $subject['teacher_id'])) {
            $assignedTeacher = $users->where('id', $subject['teacher_id'])->where('role', 'teacher')->first();
            if ($assignedTeacher) {
                $teachers[] = $assignedTeacher;
            }
        }

        return view('admin/subject_edit', [
            'pageTitle' => 'Edit Subject - TAU Student Portal',
            'activePage' => 'subjects',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Edit Subject',
            'subject' => $subject,
            'teachers' => $teachers,
            'error' => session()->getFlashdata('error'),
        ]);
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

        $subjectData = [
            'subject_code' => trim((string) $this->request->getPost('subject_code')),
            'subject_name' => trim((string) $this->request->getPost('subject_name')),
            'year_level' => $this->request->getPost('year_level'),
            'semester' => $this->request->getPost('semester'),
        ];
        if ($subjectData['subject_code'] === '' || $subjectData['subject_name'] === '') {
            session()->setFlashdata('error', 'Subject code and subject name are required.');
            return redirect()->to('admin/subjects');
        }
        if ($subjects->duplicateExists(
            $subjectData['subject_code'],
            $subjectData['subject_name'],
            (int) $subjectData['year_level'],
            $subjectData['semester']
        )) {
            session()->setFlashdata('error', 'This subject already exists for the selected year and semester.');
            return redirect()->to('admin/subjects');
        }

        $teacherQuery = (new UserModel())->where('id', $this->request->getPost('teacher_id'))
            ->where('role', 'teacher');
        if (!$teacherQuery->whereActive()->first()) {
            session()->setFlashdata('error', 'Select an active teacher for the subject.');
            return redirect()->to('admin/subjects');
        }

        $id = $subjects->insert([
            'subject_code' => $subjectData['subject_code'],
            'subject_name' => $subjectData['subject_name'],
            'units' => $this->request->getPost('units'),
            'year_level' => $subjectData['year_level'],
            'semester' => $subjectData['semester'],
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
            'school_year' => 'required',
        ];
        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('admin/subjects');
        }

        $studentId = (int) $this->request->getPost('student_id');
        $subjectId = (int) $this->request->getPost('subject_id');
        $schoolYear = trim((string) $this->request->getPost('school_year'));
        if (!SchoolYear::isAvailable($schoolYear)) {
            session()->setFlashdata('error', 'Select a valid school year.');
            return redirect()->to('admin/subjects');
        }
        if ($grades->enrollmentExists($studentId, $subjectId, $schoolYear)) {
            session()->setFlashdata('error', 'This student is already enrolled in that subject for the selected school year.');
            return redirect()->to('admin/subjects');
        }

        if (!(new SubjectModel())->find($subjectId)) {
            session()->setFlashdata('error', 'Select a valid subject to enroll the student in.');
            return redirect()->to('admin/subjects');
        }

        $studentUsers = new UserModel();
        $studentQuery = $studentUsers->where('id', $studentId)
            ->where('role', 'student');
        if (!$studentQuery->whereActive()->first()) {
            session()->setFlashdata('error', 'Select an active student to enroll.');
            return redirect()->to('admin/subjects');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $grades->insert([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'school_year' => $schoolYear,
            'grade' => null,
        ]);

        (new ActivityLogger())->log(session()->get('user_id'), session()->get('username'), 'STUDENT_ENROLLED',
            "Enrolled student #$studentId into subject #$subjectId", 'INFO');

        $db->transComplete();

        session()->setFlashdata('saved', $db->transStatus() ? 'Student enrolled.' : 'Could not enroll the student (already enrolled?).');
        return redirect()->to('admin/subjects');
    }

    public function update(int $id)
    {
        $subjects = new SubjectModel();
        $subject = $subjects->find($id);
        if (!$subject) {
            session()->setFlashdata('error', 'Subject not found.');
            return redirect()->to('admin/subjects');
        }

        $rules = [
            'subject_code' => 'required|max_length[20]',
            'subject_name' => 'required|max_length[150]',
            'units' => 'required|is_natural_no_zero|less_than_equal_to[99]',
            'year_level' => 'required|in_list[1,2,3,4]',
            'semester' => 'required|in_list[1st,2nd]',
            'teacher_id' => 'required|is_natural_no_zero',
        ];
        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to("admin/subjects/{$id}/edit");
        }

        $subjectData = [
            'subject_code' => trim((string) $this->request->getPost('subject_code')),
            'subject_name' => trim((string) $this->request->getPost('subject_name')),
            'year_level' => $this->request->getPost('year_level'),
            'semester' => $this->request->getPost('semester'),
        ];
        if ($subjectData['subject_code'] === '' || $subjectData['subject_name'] === '') {
            session()->setFlashdata('error', 'Subject code and subject name are required.');
            return redirect()->to("admin/subjects/{$id}/edit");
        }
        if ($subjects->duplicateExists(
            $subjectData['subject_code'],
            $subjectData['subject_name'],
            (int) $subjectData['year_level'],
            $subjectData['semester'],
            $id
        )) {
            session()->setFlashdata('error', 'This subject already exists for the selected year and semester.');
            return redirect()->to("admin/subjects/{$id}/edit");
        }

        $teacherId = $this->request->getPost('teacher_id');
        $teacherUsers = new UserModel();
        $assignedTeacher = $teacherUsers->where('id', $teacherId)
            ->where('role', 'teacher')->whereActive()->first();
        $unchangedInactiveTeacher = (string) $teacherId === (string) $subject['teacher_id']
            && $teacherUsers->where('id', $teacherId)->where('role', 'teacher')->first();
        if (!$assignedTeacher && !$unchangedInactiveTeacher) {
            session()->setFlashdata('error', 'Select an active teacher for the subject.');
            return redirect()->to("admin/subjects/{$id}/edit");
        }

        $updated = $subjects->update($id, [
            'subject_code' => $subjectData['subject_code'],
            'subject_name' => $subjectData['subject_name'],
            'units' => $this->request->getPost('units'),
            'year_level' => $subjectData['year_level'],
            'semester' => $subjectData['semester'],
            'teacher_id' => $teacherId,
        ]);
        if (!$updated) {
            session()->setFlashdata('error', 'Could not update the subject. Please try again.');
            return redirect()->to("admin/subjects/{$id}/edit");
        }

        (new ActivityLogger())->log(
            session()->get('user_id'),
            session()->get('username'),
            'SUBJECT_UPDATED',
            "Updated subject #$id",
            'INFO'
        );
        session()->setFlashdata('saved', 'Subject updated.');
        return redirect()->to('admin/subjects');
    }

}

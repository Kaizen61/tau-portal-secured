<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\StudentNumberSequenceModel;
use App\Models\EmployeeNumberSequenceModel;
use App\Libraries\ActivityLogger;
use App\Libraries\AcademicProgram;

class UserController extends BaseController
{
    private function filteredTeachers(UserModel $users)
    {
        $q = trim((string) $this->request->getGet('q'));
        $dateSearch = $q;
        $monthSearch = null;
        foreach (['!F j, Y' => 'F j, Y', '!M j, Y' => 'M j, Y'] as $inputFormat => $outputFormat) {
            $displayDate = \DateTime::createFromFormat($inputFormat, $q);
            if (
                $displayDate
                && strtolower($displayDate->format($outputFormat)) === strtolower($q)
                && \DateTime::getLastErrors() === false
            ) {
                $dateSearch = $displayDate->format('Y-m-d');
                break;
            }
        }
        if ($dateSearch === $q) {
            foreach (['!F' => 'F', '!M' => 'M'] as $inputFormat => $outputFormat) {
                $month = \DateTime::createFromFormat($inputFormat, $q);
                if (
                    $month
                    && strtolower($month->format($outputFormat)) === strtolower($q)
                    && \DateTime::getLastErrors() === false
                ) {
                    $monthSearch = $month->format('m');
                    break;
                }
            }
        }

        $builder = $users->where('role', 'teacher');
        if ($q !== '') {
            $builder->groupStart()
                ->like('full_name', $q)
                ->orLike('username', $q)
                ->orLike('employee_id', $q)
                ->orLike('college', $q);
            if ($monthSearch !== null) {
                $builder->orLike('created_at', '-' . $monthSearch . '-');
            }
            $builder->orLike('created_at', $dateSearch)->groupEnd();
        }

        return [
            $builder->where('username !=', 'backup_admin')->orderBy('full_name'),
            ['q' => $q],
        ];
    }

    public function index()
    {
        [$builder, $filters] = $this->filteredTeachers(new UserModel());
        $data = [
            'pageTitle' => 'Teachers - TAU Student Portal',
            'activePage' => 'users',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Teachers',
            'users' => $builder->findAll(),
            ...$filters,
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ];
        return view('admin/users', $data);
    }

    public function printDirectory()
    {
        [$builder] = $this->filteredTeachers(new UserModel());

        return view('admin/print_teachers', [
            'printTitle' => 'Teacher Directory - TAU Student Portal',
            'documentTitle' => 'Teacher Directory',
            'printOrientation' => 'landscape',
            'printedAt' => date('F j, Y g:i A'),
            'autoPrint' => false,
            'teachers' => $builder->findAll(),
            'filters' => array_filter([
                'Search' => trim((string) $this->request->getGet('q')),
            ]),
        ]);
    }

    public function addAccount()
    {
        return view('admin/add_account', [
            'pageTitle' => 'Add Account - TAU Student Portal',
            'activePage' => 'add-account',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Add Account',
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
            'studentNumberPreview' => (new StudentNumberSequenceModel())->nextNumberPreview(),
            'employeeNumberPreview' => (new EmployeeNumberSequenceModel())->nextNumberPreview(),
            'programsByCollege' => AcademicProgram::PROGRAMS_BY_COLLEGE,
        ]);
    }

    public function edit(int $id)
    {
        $user = (new UserModel())->find($id);
        if (!$user || !in_array($user['role'], ['student', 'teacher'], true)) {
            session()->setFlashdata('error', 'Only student and teacher accounts can be edited.');
            return redirect()->to('admin/users');
        }

        return view('admin/user_edit', [
            'pageTitle' => 'Edit User - TAU Student Portal',
            'activePage' => $user['role'] === 'student' ? 'students' : 'users',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Edit User Account',
            'user' => $user,
            'returnUrl' => $user['role'] === 'student' ? 'admin/students' : 'admin/users',
            'error' => session()->getFlashdata('error'),
            'programsByCollege' => AcademicProgram::PROGRAMS_BY_COLLEGE,
        ]);
    }

    public function create()
    {
        $users = new UserModel();
        $role = $this->request->getPost('role');
        $isStudent = $role === 'student';
        $fullName = trim((string) $this->request->getPost('full_name'));
        $username = trim((string) $this->request->getPost('username'));

        $rules = [
            'full_name' => 'required|min_length[2]|max_length[100]',
            'password' => 'required|min_length[12]',
            'role' => 'required|in_list[student,teacher,admin]',
            'college' => 'permit_empty|in_list[College of Veterinary Medicine (CVM),College of Engineering and Technology (CET),College of Education (CEd),College of Business and Management (CBM),College of Arts and Sciences (CAS),College of Agriculture and Forestry (CAF)]',
        ];
        if ($role === 'teacher') {
            $rules['college'] = 'required|in_list[College of Veterinary Medicine (CVM),College of Engineering and Technology (CET),College of Education (CEd),College of Business and Management (CBM),College of Arts and Sciences (CAS),College of Agriculture and Forestry (CAF)]';
        }
        if (!$isStudent) {
            $rules['username'] = 'required|max_length[50]';
        } else {
            $rules['college'] = 'required|in_list[College of Veterinary Medicine (CVM),College of Engineering and Technology (CET),College of Education (CEd),College of Business and Management (CBM),College of Arts and Sciences (CAS),College of Agriculture and Forestry (CAF)]';
            $rules['program'] = 'required|max_length[10]';
            $rules['year_level'] = 'required|in_list[1st Year,2nd Year,3rd Year,4th Year,5th Year,6th Year]';
            $rules['section'] = 'required|in_list[A,B,C]';
        }

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('admin/add-account');
        }
        if (mb_strlen($fullName) < 2) {
            session()->setFlashdata('error', 'Full name must contain at least 2 non-space characters.');
            return redirect()->to('admin/add-account');
        }
        if (!$isStudent && $username === '') {
            session()->setFlashdata('error', 'Username is required.');
            return redirect()->to('admin/add-account');
        }
        if ($isStudent && !AcademicProgram::belongsToCollege(
            trim((string) $this->request->getPost('program')),
            trim((string) $this->request->getPost('college'))
        )) {
            session()->setFlashdata('error', 'Select a program offered by the selected college.');
            return redirect()->to('admin/add-account');
        }

        if (!$isStudent && $users->identifierExists('username', $username)) {
            session()->setFlashdata('error', 'That username is already assigned to another account.');
            return redirect()->to('admin/add-account');
        }
        $college = $role === 'admin' ? null : trim((string) $this->request->getPost('college'));
        if ($users->accountIdentityExists($role, $fullName, $college)) {
            session()->setFlashdata('error', 'An account with the same name and college already exists for this role.');
            return redirect()->to('admin/add-account');
        }

        $newUser = [
            'username' => $isStudent ? null : $username,
            'student_number' => null,
            'employee_id' => null,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $role,
            'full_name' => $fullName,
            'college' => $college,
            'program' => $isStudent ? $this->request->getPost('program') : null,
            'year_level' => $isStudent ? $this->request->getPost('year_level') : null,
            'section' => $isStudent ? $this->request->getPost('section') : null,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        if ($users->hasActiveStatusColumn()) {
            $newUser['is_active'] = 1;
        }

        $studentNumber = null;
        $employeeId = null;
        if ($isStudent || in_array($role, ['teacher', 'admin'], true)) {
            $db = db_connect();
            $db->transBegin();
            try {
                if ($isStudent) {
                $studentNumber = (new StudentNumberSequenceModel())->allocateNextNumber();
                $newUser['student_number'] = $studentNumber;
                } else {
                    $employeeId = (new EmployeeNumberSequenceModel())->allocateNextNumber();
                    $newUser['employee_id'] = $employeeId;
                }
                $id = $users->insert($newUser);
                if (!$id || !$db->transStatus()) {
                    $db->transRollback();
                    session()->setFlashdata('error', 'Could not create the account. Please try again.');
                    return redirect()->to('admin/add-account');
                }
                $db->transCommit();
            } catch (\RuntimeException $exception) {
                $db->transRollback();
                session()->setFlashdata('error', $exception->getMessage());
                return redirect()->to('admin/add-account');
            }
        } else {
            $id = $users->insert($newUser);
            if (!$id) {
                session()->setFlashdata('error', 'Could not create the account. Please try again.');
                return redirect()->to('admin/add-account');
            }
        }

        (new ActivityLogger())->log(session()->get('user_id'), session()->get('username'), 'ACCOUNT_CREATED',
            "Created $role account (user #$id"
                . ($studentNumber ? ", student number $studentNumber" : '')
                . ($employeeId ? ", employee ID $employeeId" : '') . ')', 'INFO');

        session()->setFlashdata('saved', 'User account created.');
        return redirect()->to($role === 'teacher' ? 'admin/users' : ($role === 'student' ? 'admin/students' : 'admin/add-account'));
    }

    public function update(int $id)
    {
        $users = new UserModel();
        $user = $users->find($id);
        if (!$user || !in_array($user['role'], ['student', 'teacher'], true)) {
            session()->setFlashdata('error', 'Only student and teacher accounts can be edited.');
            return redirect()->to('admin/users');
        }
        $returnUrl = $user['role'] === 'student' ? 'admin/students' : 'admin/users';

        $isStudent = $user['role'] === 'student';
        $rules = [
            'full_name' => 'required|min_length[2]|max_length[100]',
            'college' => 'permit_empty|in_list[College of Veterinary Medicine (CVM),College of Engineering and Technology (CET),College of Education (CEd),College of Business and Management (CBM),College of Arts and Sciences (CAS),College of Agriculture and Forestry (CAF)]',
            'password' => 'permit_empty|min_length[12]',
        ];
        if ($isStudent) {
            $rules['college'] = 'required|in_list[College of Veterinary Medicine (CVM),College of Engineering and Technology (CET),College of Education (CEd),College of Business and Management (CBM),College of Arts and Sciences (CAS),College of Agriculture and Forestry (CAF)]';
            $rules['program'] = 'required|max_length[10]';
            $rules['year_level'] = 'required|in_list[1st Year,2nd Year,3rd Year,4th Year,5th Year,6th Year]';
            $rules['section'] = 'required|in_list[A,B,C]';
        } else {
            $rules['college'] = 'required|in_list[College of Veterinary Medicine (CVM),College of Engineering and Technology (CET),College of Education (CEd),College of Business and Management (CBM),College of Arts and Sciences (CAS),College of Agriculture and Forestry (CAF)]';
            $rules['username'] = 'required|max_length[50]';
        }

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to("admin/users/{$id}/edit");
        }

        $fullName = trim((string) $this->request->getPost('full_name'));
        if (mb_strlen($fullName) < 2) {
            session()->setFlashdata('error', 'Full name must contain at least 2 characters.');
            return redirect()->to("admin/users/{$id}/edit");
        }
        if ($isStudent && !AcademicProgram::belongsToCollege(
            trim((string) $this->request->getPost('program')),
            trim((string) $this->request->getPost('college'))
        )) {
            session()->setFlashdata('error', 'Select a program offered by the selected college.');
            return redirect()->to("admin/users/{$id}/edit");
        }

        $identifier = null;
        if (!$isStudent) {
            $identifier = trim((string) $this->request->getPost('username'));
            if ($identifier === '') {
                session()->setFlashdata('error', 'Username is required.');
                return redirect()->to("admin/users/{$id}/edit");
            }
            if ($users->identifierExists('username', $identifier, $id)) {
                session()->setFlashdata('error', 'That username is already assigned to another account.');
                return redirect()->to("admin/users/{$id}/edit");
            }
            $college = trim((string) $this->request->getPost('college'));
            if ($users->accountIdentityExists($user['role'], $fullName, $college, $id)) {
                session()->setFlashdata('error', 'An account with the same name and college already exists for this role.');
                return redirect()->to("admin/users/{$id}/edit");
            }
        }

        $update = [
            'full_name' => $fullName,
            'college' => trim((string) $this->request->getPost('college')) ?: null,
        ];
        if ($isStudent) {
            $update['program'] = $this->request->getPost('program');
            $update['year_level'] = $this->request->getPost('year_level');
            $update['section'] = $this->request->getPost('section');
        } else {
            $update['username'] = $identifier;
        }

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $update['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (!$users->update($id, $update)) {
            session()->setFlashdata('error', 'Could not update the user account. Please try again.');
            return redirect()->to("admin/users/{$id}/edit");
        }
        (new ActivityLogger())->log(
            session()->get('user_id'),
            session()->get('username'),
            'ACCOUNT_UPDATED',
            "Updated {$user['role']} account (user #$id)",
            'INFO'
        );

        session()->setFlashdata('saved', 'User account updated.');
        return redirect()->to($returnUrl);
    }

    public function deactivate(int $id)
    {
        return $this->setAccountActive($id, false);
    }

    public function activate(int $id)
    {
        return $this->setAccountActive($id, true);
    }

    private function setAccountActive(int $id, bool $active)
    {
        $users = new UserModel();
        $user = $users->find($id);
        if (!$user || !in_array($user['role'], ['student', 'teacher'], true) || ($user['username'] ?? '') === 'backup_admin') {
            session()->setFlashdata('error', 'Only student and teacher accounts can be activated or deactivated.');
            return redirect()->to('admin/users');
        }
        if (!$users->hasActiveStatusColumn()) {
            session()->setFlashdata('error', 'Account status is unavailable until the is_active database column is added.');
            return redirect()->to($user['role'] === 'student' ? 'admin/students' : 'admin/users');
        }

        if ((bool) ($user['is_active'] ?? true) !== $active && !$users->update($id, ['is_active' => $active ? 1 : 0])) {
            session()->setFlashdata('error', 'Could not update the account status. Please try again.');
            return redirect()->to($user['role'] === 'student' ? 'admin/students' : 'admin/users');
        }

        $action = $active ? 'ACCOUNT_ACTIVATED' : 'ACCOUNT_DEACTIVATED';
        (new ActivityLogger())->log(
            session()->get('user_id'),
            session()->get('username'),
            $action,
            ucfirst($user['role']) . " account (user #$id)",
            $active ? 'INFO' : 'WARNING'
        );

        session()->setFlashdata('saved', $active ? 'Account reactivated.' : 'Account deactivated.');
        return redirect()->to($user['role'] === 'student' ? 'admin/students' : 'admin/users');
    }
}

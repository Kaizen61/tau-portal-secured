<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\AcademicProgram;

class StudentController extends BaseController
{
    private function filteredStudents(UserModel $users): array
    {
        $q = trim((string) $this->request->getGet('q'));
        $sort = $this->request->getGet('sort');
        $sort = in_array($sort, ['asc', 'desc'], true) ? $sort : 'asc';
        $college = $this->request->getGet('college');
        $colleges = [
            'College of Veterinary Medicine (CVM)',
            'College of Engineering and Technology (CET)',
            'College of Education (CEd)',
            'College of Business and Management (CBM)',
            'College of Arts and Sciences (CAS)',
            'College of Agriculture and Forestry (CAF)',
        ];
        $college = in_array($college, $colleges, true) ? $college : '';
        $yearLevel = $this->request->getGet('year_level');
        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', '6th Year'];
        $yearLevel = in_array($yearLevel, $yearLevels, true) ? $yearLevel : '';
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

        $builder = $users->where('role', 'student');
        if ($college !== '') {
            $builder->where('college', $college);
        }
        if ($yearLevel !== '') {
            $builder->where('year_level', $yearLevel);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('full_name', $q)
                ->orLike('student_number', $q)
                ->orLike('college', $q)
                ->orLike('program', $q)
                ->orLike('year_level', $q);
            if ($monthSearch !== null) {
                $builder->orLike('created_at', '-' . $monthSearch . '-');
            }
            $builder->orLike('created_at', $dateSearch)->groupEnd();
        }

        return [
            $builder->orderBy('full_name', strtoupper($sort))->orderBy('student_number'),
            [
                'q' => $q,
                'sort' => $sort,
                'collegeFilter' => $college,
                'yearLevelFilter' => $yearLevel,
                'colleges' => $colleges,
                'yearLevels' => $yearLevels,
            ],
        ];
    }

    public function index()
    {
        $users = new UserModel();
        [$builder, $filters] = $this->filteredStudents($users);
        $data = [
            'pageTitle' => 'Students - TAU Student Portal',
            'activePage' => 'students',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Students',
            'students' => $builder->findAll(),
            ...$filters,
            'programsByCollege' => AcademicProgram::PROGRAMS_BY_COLLEGE,
            'programNames' => array_merge(...array_values(AcademicProgram::PROGRAMS_BY_COLLEGE)),
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ];
        return view('admin/students', $data);
    }

    public function printDirectory()
    {
        [$builder, $directoryFilters] = $this->filteredStudents(new UserModel());

        return view('admin/print_students', [
            'printTitle' => 'Student Directory - TAU Student Portal',
            'documentTitle' => 'Student Directory',
            'printOrientation' => 'landscape',
            'printedAt' => date('F j, Y g:i A'),
            'autoPrint' => false,
            'students' => $builder->findAll(),
            'programNames' => array_merge(...array_values(AcademicProgram::PROGRAMS_BY_COLLEGE)),
            'filters' => array_filter([
                'Search' => $directoryFilters['q'],
                'Sort' => $directoryFilters['sort'] === 'asc' ? 'Name: A to Z' : 'Name: Z to A',
                'College' => $directoryFilters['collegeFilter'],
                'Year Level' => $directoryFilters['yearLevelFilter'],
            ]),
        ]);
    }
}

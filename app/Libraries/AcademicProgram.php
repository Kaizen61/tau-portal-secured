<?php

namespace App\Libraries;

final class AcademicProgram
{
    public const PROGRAMS_BY_COLLEGE = [
        'College of Veterinary Medicine (CVM)' => [
            'DVM' => 'Doctor of Veterinary Medicine',
        ],
        'College of Engineering and Technology (CET)' => [
            'BSGE' => 'Bachelor of Science in Geodetic Engineering',
            'BSIT' => 'Bachelor of Science in Information Technology',
            'BSABE' => 'Bachelor of Science in Agricultural and Biosystems Engineering',
        ],
        'College of Education (CEd)' => [
            'BEEd' => 'Bachelor of Elementary Education',
            'BECEd' => 'Bachelor of Early Childhood Education',
            'BSEd' => 'Bachelor of Secondary Education',
            'BTLEd' => 'Bachelor of Technology and Livelihood Education',
        ],
        'College of Business and Management (CBM)' => [
            'BSAB' => 'Bachelor of Science in Agribusiness',
            'BSE' => 'Bachelor of Science in Entrepreneurship',
            'BSTM' => 'Bachelor of Science in Tourism Management',
            'BSBA' => 'Bachelor of Science in Business Administration',
        ],
        'College of Arts and Sciences (CAS)' => [
            'BSP' => 'Bachelor of Science in Psychology',
            'BSDC' => 'Bachelor of Science in Development Communication',
            'BAE' => 'Bachelor of Arts in Economics',
        ],
        'College of Agriculture and Forestry (CAF)' => [
            'BSA' => 'Bachelor of Science in Agriculture',
            'BAS' => 'Bachelor of Animal Science',
            'BSFT' => 'Bachelor of Science in Food Technology',
            'BSF' => 'Bachelor of Science in Forestry',
        ],
    ];

    public static function belongsToCollege(string $program, string $college): bool
    {
        return isset(self::PROGRAMS_BY_COLLEGE[$college][$program]);
    }
}

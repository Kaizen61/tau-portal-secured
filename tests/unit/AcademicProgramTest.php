<?php

use App\Libraries\AcademicProgram;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AcademicProgramTest extends CIUnitTestCase
{
    public function testProgramMappingsMatchColleges(): void
    {
        $this->assertTrue(AcademicProgram::belongsToCollege(
            'BSIT',
            'College of Engineering and Technology (CET)'
        ));
        $this->assertTrue(AcademicProgram::belongsToCollege(
            'BSEd',
            'College of Education (CEd)'
        ));
        $this->assertTrue(AcademicProgram::belongsToCollege(
            'BSF',
            'College of Agriculture and Forestry (CAF)'
        ));
        $this->assertFalse(AcademicProgram::belongsToCollege(
            'DVM',
            'College of Arts and Sciences (CAS)'
        ));
    }
}

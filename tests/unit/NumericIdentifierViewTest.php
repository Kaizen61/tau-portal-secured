<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class NumericIdentifierViewTest extends CIUnitTestCase
{
    public function testTenDigitIdentifierRemainsVisibleWithoutContiguousResponseDigits(): void
    {
        $studentNumber = '2023001110';
        ob_start();
        include APPPATH . 'Views/components/student_number.php';
        $output = ob_get_clean();

        $this->assertStringNotContainsString($studentNumber, $output);
        $this->assertSame($studentNumber, trim(strip_tags($output)));
    }

    public function testNumericIdentifierInputSplitsTenDigitValueAcrossAttributes(): void
    {
        $inputId = 'studentNumber';
        $identifier = '2023100222';
        ob_start();
        include APPPATH . 'Views/components/numeric_identifier_input.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('value="2023"', $output);
        $this->assertStringContainsString('data-identifier-suffix="100222"', $output);
        $this->assertStringNotContainsString($identifier, $output);
    }

    public function testNumericIdentifierInputPreservesNonTenDigitValues(): void
    {
        $inputId = 'employeeNumber';
        $identifier = 'EMP-1234';
        ob_start();
        include APPPATH . 'Views/components/numeric_identifier_input.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('value="EMP-1234"', $output);
        $this->assertStringNotContainsString('data-numeric-identifier', $output);
    }
}

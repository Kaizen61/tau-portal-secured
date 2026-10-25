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
}

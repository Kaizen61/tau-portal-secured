<?php

use App\Controllers\Student\TeacherEvaluationController;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TeacherEvaluationChoiceTest extends CIUnitTestCase
{
    public function testChoiceTokensResolveOnlyToEligibleTeachers(): void
    {
        $controller = (new ReflectionClass(TeacherEvaluationController::class))->newInstanceWithoutConstructor();
        $addTokens = new ReflectionMethod($controller, 'addTeacherChoiceTokens');
        $resolve = new ReflectionMethod($controller, 'resolveTeacherId');
        $choices = $addTokens->invoke($controller, [
            ['teacher_id' => 15],
            ['teacher_id' => 20],
        ]);

        $this->assertSame('teacher-choice-1', $choices[0]['choice_token']);
        $this->assertSame('teacher-choice-2', $choices[1]['choice_token']);
        $this->assertStringNotContainsString('15', $choices[0]['choice_token']);
        $this->assertSame(15, $resolve->invoke($controller, $choices, 'teacher-choice-1'));
        $this->assertSame(15, $resolve->invoke($controller, $choices, '15'));
        $this->assertNull($resolve->invoke($controller, $choices, '<img src=x>'));
    }
}

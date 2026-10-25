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

        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $choices[0]['choice_token']);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $choices[1]['choice_token']);
        $this->assertNotSame($choices[0]['choice_token'], $choices[1]['choice_token']);
        $this->assertNotSame('15', $choices[0]['choice_token']);
        $this->assertSame(15, $resolve->invoke($controller, $choices, $choices[0]['choice_token']));
        $this->assertSame(15, $resolve->invoke($controller, $choices, '15'));
        $this->assertSame(15, $resolve->invoke($controller, $choices, 'teacher-choice-1'));
        $this->assertNull($resolve->invoke($controller, $choices, '<img src=x>'));
    }
}

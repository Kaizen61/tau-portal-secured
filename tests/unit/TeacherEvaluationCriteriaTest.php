<?php

use App\Libraries\TeacherEvaluationCriteria;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TeacherEvaluationCriteriaTest extends CIUnitTestCase
{
    public function testSectionsContainAllTwentyTwoUniqueCriteria(): void
    {
        $sectionCriteria = [];
        foreach (TeacherEvaluationCriteria::SECTIONS as $criteria) {
            $sectionCriteria += $criteria;
        }

        $this->assertCount(22, TeacherEvaluationCriteria::CRITERIA);
        $this->assertSame(TeacherEvaluationCriteria::CRITERIA, $sectionCriteria);
        $this->assertCount(22, array_unique(array_keys(TeacherEvaluationCriteria::CRITERIA)));
    }

    public function testCommentFieldsHaveExpectedStorageNames(): void
    {
        $this->assertSame(
            ['strengths', 'improvements', 'additional_comments'],
            array_keys(TeacherEvaluationCriteria::COMMENT_FIELDS)
        );
    }
}

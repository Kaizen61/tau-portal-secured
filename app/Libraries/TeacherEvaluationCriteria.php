<?php

namespace App\Libraries;

class TeacherEvaluationCriteria
{
    public const SECTIONS = [
        'Teaching Effectiveness' => [
            'clear_explanations' => 'Explains lessons clearly and understandably.',
            'subject_mastery' => 'Demonstrates mastery of the subject matter.',
            'logical_organization' => 'Organizes lessons logically and systematically.',
            'teaching_strategies' => 'Uses appropriate teaching strategies and learning materials.',
            'practical_applications' => 'Connects lessons to practical applications and real-world situations.',
        ],
        'Classroom Management' => [
            'class_timing' => 'Starts and ends classes appropriately.',
            'class_time' => 'Uses class time effectively.',
            'learning_environment' => 'Maintains a respectful and productive learning environment.',
            'student_participation' => 'Encourages student participation and engagement.',
            'class_expectations' => 'Communicates class rules, requirements, and expectations clearly.',
        ],
        'Assessment and Feedback' => [
            'assessment_alignment' => 'Aligns quizzes, examinations, and activities with the lessons taught.',
            'grading_criteria' => 'Explains grading criteria and assessment requirements clearly.',
            'constructive_feedback' => 'Provides timely and constructive feedback.',
            'fair_grading' => 'Grades student work fairly and consistently.',
            'improvement_opportunities' => 'Provides opportunities for students to clarify their mistakes and improve.',
        ],
        'Professionalism and Student Support' => [
            'respect_and_fairness' => 'Treats students with respect and fairness.',
            'professional_conduct' => 'Demonstrates professionalism and appropriate conduct.',
            'academic_assistance' => 'Is approachable when students need academic assistance.',
            'student_responses' => "Responds appropriately to students' questions and concerns.",
            'student_development' => 'Encourages students to learn, improve, and develop their abilities.',
        ],
        'Overall Evaluation' => [
            'learning_facilitation' => 'Overall, the teacher effectively facilitates student learning.',
            'instruction_satisfaction' => 'Overall, I am satisfied with the quality of instruction provided.',
        ],
    ];

    public const CRITERIA = [
        'clear_explanations' => 'Explains lessons clearly and understandably.',
        'subject_mastery' => 'Demonstrates mastery of the subject matter.',
        'logical_organization' => 'Organizes lessons logically and systematically.',
        'teaching_strategies' => 'Uses appropriate teaching strategies and learning materials.',
        'practical_applications' => 'Connects lessons to practical applications and real-world situations.',
        'class_timing' => 'Starts and ends classes appropriately.',
        'class_time' => 'Uses class time effectively.',
        'learning_environment' => 'Maintains a respectful and productive learning environment.',
        'student_participation' => 'Encourages student participation and engagement.',
        'class_expectations' => 'Communicates class rules, requirements, and expectations clearly.',
        'assessment_alignment' => 'Aligns quizzes, examinations, and activities with the lessons taught.',
        'grading_criteria' => 'Explains grading criteria and assessment requirements clearly.',
        'constructive_feedback' => 'Provides timely and constructive feedback.',
        'fair_grading' => 'Grades student work fairly and consistently.',
        'improvement_opportunities' => 'Provides opportunities for students to clarify their mistakes and improve.',
        'respect_and_fairness' => 'Treats students with respect and fairness.',
        'professional_conduct' => 'Demonstrates professionalism and appropriate conduct.',
        'academic_assistance' => 'Is approachable when students need academic assistance.',
        'student_responses' => "Responds appropriately to students' questions and concerns.",
        'student_development' => 'Encourages students to learn, improve, and develop their abilities.',
        'learning_facilitation' => 'Overall, the teacher effectively facilitates student learning.',
        'instruction_satisfaction' => 'Overall, I am satisfied with the quality of instruction provided.',
    ];

    public const COMMENT_FIELDS = [
        'strengths' => "What are the teacher's strengths?",
        'improvements' => 'What improvements would you recommend?',
        'additional_comments' => 'Additional comments or suggestions (optional):',
    ];
}

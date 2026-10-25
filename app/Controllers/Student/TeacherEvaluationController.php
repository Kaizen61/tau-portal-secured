<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\AcademicSemester;
use App\Libraries\TeacherEvaluationCriteria;
use App\Libraries\SchoolYear;
use App\Models\TeacherEvaluationModel;

class TeacherEvaluationController extends BaseController
{
    public function index()
    {
        $studentId = (int) session()->get('user_id');
        $evaluations = new TeacherEvaluationModel();
        $semester = AcademicSemester::current();
        $eligibleTeachers = $semester === null
            ? []
            : $evaluations->eligibleTeachersForStudent($studentId, $semester);
        $requestedTeacher = $this->request->getGet('teacher_id');
        $selectedTeacherId = $this->resolveTeacherId($eligibleTeachers, $requestedTeacher);
        $teacherChoices = $this->addTeacherChoiceTokens($eligibleTeachers);
        $selectedTeacherToken = null;
        foreach ($teacherChoices as $teacher) {
            if ((int) $teacher['teacher_id'] === $selectedTeacherId) {
                $selectedTeacherToken = $teacher['choice_token'];
                break;
            }
        }
        $teachers = $selectedTeacherId === null
            ? []
            : array_values(array_filter(
                $teacherChoices,
                static fn (array $teacher): bool => (int) $teacher['teacher_id'] === $selectedTeacherId
            ));

        return view('student/teacher_evaluations', [
            'pageTitle' => 'Teacher Evaluations - TAU Student Portal',
            'activePage' => 'teacher-evaluations',
            'eyebrow' => 'Student',
            'pageHeading' => 'Teacher Evaluations',
            'schoolYear' => SchoolYear::current(),
            'semester' => $semester,
            'criteria' => TeacherEvaluationCriteria::CRITERIA,
            'sections' => TeacherEvaluationCriteria::SECTIONS,
            'commentFields' => TeacherEvaluationCriteria::COMMENT_FIELDS,
            'teacherChoices' => $teacherChoices,
            'selectedTeacherId' => $selectedTeacherId,
            'selectedTeacherToken' => $selectedTeacherToken,
            'teachers' => $teachers,
            'submittedEvaluations' => $semester === null
                ? []
                : $evaluations->evaluationsForStudent($studentId, $semester),
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function submit()
    {
        $studentId = (int) session()->get('user_id');
        $semester = AcademicSemester::current();
        $evaluations = new TeacherEvaluationModel();
        $eligibleTeachers = $semester === null
            ? []
            : $evaluations->eligibleTeachersForStudent($studentId, $semester);
        $postedTeacher = $this->request->getPost('teacher_id');
        $teacherId = $this->resolveTeacherId($eligibleTeachers, $postedTeacher);
        $submittedScores = $this->request->getPost('scores');
        $criteria = TeacherEvaluationCriteria::CRITERIA;
        $redirectUrl = 'student/teacher-evaluations';
        if ($teacherId !== null && (is_string($postedTeacher) || is_int($postedTeacher))) {
            $redirectUrl .= '?teacher_id=' . rawurlencode((string) $postedTeacher);
        }

        if ($semester === null) {
            session()->setFlashdata('error', 'Teacher evaluations are available only during the 1st semester (August–December) and 2nd semester (January–May).');
            return redirect()->to($redirectUrl);
        }

        if (!$teacherId || !is_array($submittedScores)) {
            session()->setFlashdata('error', 'The evaluation submission is invalid.');
            return redirect()->to($redirectUrl);
        }

        $submittedKeys = array_keys($submittedScores);
        $expectedKeys = array_keys($criteria);
        sort($submittedKeys);
        sort($expectedKeys);
        if ($submittedKeys !== $expectedKeys) {
            session()->setFlashdata('error', 'Please rate every evaluation criterion.');
            return redirect()->to($redirectUrl);
        }

        $scores = [];
        foreach ($criteria as $criterion => $label) {
            $value = $submittedScores[$criterion];
            if ((!is_string($value) && !is_int($value)) || !ctype_digit((string) $value) || !in_array((int) $value, [1, 2, 3, 4, 5], true)) {
                session()->setFlashdata('error', 'Each rating must be a whole number from 1 to 5.');
                return redirect()->to($redirectUrl);
            }
            $scores[$criterion] = (int) $value;
        }

        $comments = [];
        foreach (TeacherEvaluationCriteria::COMMENT_FIELDS as $field => $label) {
            $value = $this->request->getPost($field);
            if ($value !== null && !is_string($value)) {
                session()->setFlashdata('error', 'The comment fields must contain text only.');
                return redirect()->to($redirectUrl);
            }
            $comment = trim((string) $value);
            if (mb_strlen($comment) > 5000) {
                session()->setFlashdata('error', 'Each comment must be 5,000 characters or fewer.');
                return redirect()->to($redirectUrl);
            }
            $comments[$field] = $comment === '' ? null : $comment;
        }

        $eligibleTeacherIds = array_column($evaluations->eligibleTeachersForStudent($studentId, $semester), 'teacher_id');
        if (!in_array((int) $teacherId, array_map('intval', $eligibleTeacherIds), true)) {
            session()->setFlashdata('error', 'You can evaluate only teachers whose subjects you are enrolled in.');
            return redirect()->to($redirectUrl);
        }

        if ($evaluations->hasSubmitted($studentId, (int) $teacherId, $semester)) {
            session()->setFlashdata('error', 'You have already submitted an evaluation for this teacher. Submitted evaluations cannot be changed.');
            return redirect()->to($redirectUrl);
        }

        $db = db_connect();
        $db->transBegin();
        $evaluationId = $evaluations->insert([
            'student_id' => $studentId,
            'teacher_id' => (int) $teacherId,
            'school_year' => SchoolYear::current(),
            'semester' => $semester,
            'submitted_at' => date('Y-m-d H:i:s'),
            ...$comments,
        ], true);

        if (!$evaluationId) {
            $db->transRollback();
            session()->setFlashdata('error', 'Could not save the evaluation. Please try again.');
            return redirect()->to($redirectUrl);
        }

        $scoreRows = [];
        foreach ($scores as $criterion => $score) {
            $scoreRows[] = [
                'evaluation_id' => (int) $evaluationId,
                'criterion' => $criterion,
                'score' => $score,
            ];
        }

        if (!$db->table('teacher_evaluation_scores')->insertBatch($scoreRows) || !$db->transStatus()) {
            $db->transRollback();
            session()->setFlashdata('error', 'Could not save all evaluation ratings. Please try again.');
            return redirect()->to($redirectUrl);
        }

        $logged = (new ActivityLogger())->log(
            $studentId,
            (string) session()->get('username'),
            'TEACHER_EVALUATION_SUBMITTED',
            sprintf(
                'Submitted evaluation #%d for teacher account #%d, School Year %s, %s semester.',
                (int) $evaluationId,
                (int) $teacherId,
                SchoolYear::current(),
                $semester
            )
        );
        if (!$logged || !$db->transStatus()) {
            $db->transRollback();
            session()->setFlashdata('error', 'Could not record the evaluation in System Activity Logs. The evaluation was not saved; please try again.');
            return redirect()->to($redirectUrl);
        }

        $db->transCommit();
        session()->setFlashdata('saved', 'Your teacher evaluation was submitted and cannot be changed.');
        return redirect()->to($redirectUrl);
    }

    private function addTeacherChoiceTokens(array $choices): array
    {
        $tokenMap = session()->get('teacher_evaluation_choice_tokens');
        $tokenMap = is_array($tokenMap) ? $tokenMap : [];

        foreach ($choices as &$choice) {
            $token = bin2hex(random_bytes(16));
            $tokenMap[$token] = (int) $choice['teacher_id'];
            $choice['choice_token'] = $token;
        }
        unset($choice);

        $tokenLimit = max(100, count($choices));
        if (count($tokenMap) > $tokenLimit) {
            $tokenMap = array_slice($tokenMap, -$tokenLimit, null, true);
        }
        session()->set('teacher_evaluation_choice_tokens', $tokenMap);

        return $choices;
    }

    private function resolveTeacherId(array $choices, $requestedTeacher): ?int
    {
        if (!is_string($requestedTeacher) && !is_int($requestedTeacher)) {
            return null;
        }

        if (is_string($requestedTeacher)) {
            $tokenMap = session()->get('teacher_evaluation_choice_tokens');
            if (is_array($tokenMap) && isset($tokenMap[$requestedTeacher])) {
                $mappedTeacherId = (int) $tokenMap[$requestedTeacher];
                foreach ($choices as $choice) {
                    if ((int) $choice['teacher_id'] === $mappedTeacherId) {
                        return $mappedTeacherId;
                    }
                }
            }
            if (preg_match('/^teacher-choice-([1-9]\d*)$/D', $requestedTeacher, $matches) === 1) {
                $legacyIndex = (int) $matches[1] - 1;
                if (isset($choices[$legacyIndex])) {
                    return (int) $choices[$legacyIndex]['teacher_id'];
                }
            }
        }

        $legacyTeacherId = filter_var($requestedTeacher, FILTER_VALIDATE_INT);
        if ($legacyTeacherId !== false) {
            foreach ($choices as $choice) {
                if ((int) $choice['teacher_id'] === (int) $legacyTeacherId) {
                    return (int) $choice['teacher_id'];
                }
            }
        }

        return null;
    }
}

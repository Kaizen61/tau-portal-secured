<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Evaluations are available only for your currently active semester and only for teachers whose subjects you are enrolled in. The 1st semester runs August–December; the 2nd semester runs January–May. You can submit one evaluation per teacher per semester.</p>

    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

    <section class="card portal-card mb-3">
        <div class="card-header bg-white"><h2 class="h6 mb-0">Rating Scale</h2></div>
        <div class="card-body">
            <div class="teacher-evaluation-rating-legend">
                <?php foreach ([5 => 'Excellent', 4 => 'Very Good', 3 => 'Good', 2 => 'Fair', 1 => 'Poor'] as $value => $description): ?>
                    <span><strong><?= $value ?></strong> <?= esc($description) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($semester === null): ?>
        <div class="card portal-card"><div class="card-body text-muted">Teacher evaluations are currently closed. The 1st semester evaluation period is August–December, and the 2nd semester evaluation period is January–May.</div></div>
    <?php else: ?>
    <section class="card portal-card mb-3">
        <div class="card-body">
            <form method="GET" action="<?= site_url('student/teacher-evaluations') ?>" class="d-flex flex-wrap align-items-end gap-3">
                <div>
                    <p class="form-label fw-semibold mb-2">Current semester: <?= esc($semester) ?> Semester · School Year <?= esc($schoolYear) ?></p>
                    <label for="evaluation-teacher-filter" class="form-label fw-semibold">Teacher</label>
                    <select id="evaluation-teacher-filter" name="teacher_id" class="form-select teacher-evaluation-teacher-select">
                        <option value="">Choose one teacher</option>
                        <?php foreach ($teacherChoices as $choice): ?>
                            <option value="<?= (int) $choice['teacher_id'] ?>" <?= $selectedTeacherId === (int) $choice['teacher_id'] ? 'selected' : '' ?>>
                                <?= esc($choice['teacher_name']) ?><?= $choice['submitted'] ? ' — Evaluation submitted' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-outline-university">Show Evaluation</button>
            </form>
        </div>
    </section>

    <?php if (empty($teachers)): ?>
        <?php if (empty($teacherChoices)): ?>
            <div class="card portal-card"><div class="card-body text-muted">You are not currently enrolled in any teacher-led subjects for this semester.</div></div>
        <?php else: ?>
            <div class="card portal-card"><div class="card-body text-muted">Choose one teacher above to view or complete their current-semester evaluation.</div></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($teachers as $teacher): ?>
                <div class="col-12">
                    <section class="card portal-card teacher-evaluation-card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <h2 class="h5 mb-1"><?= esc($teacher['teacher_name']) ?></h2>
                                <p class="small text-muted mb-0">
                                    <?= esc($teacher['employee_id'] ?? 'Faculty') ?>
                                    <?= !empty($teacher['college']) ? ' · ' . esc($teacher['college']) : '' ?>
                                </p>
                                <p class="small text-muted mb-0">Your subjects: <?php foreach ($teacher['subjects'] as $index => $subject): ?><?= $index > 0 ? ', ' : '' ?><?= esc($subject['subject_code']) ?> — <?= esc($subject['subject_name']) ?><?php endforeach; ?></p>
                            </div>
                            <?php if ($teacher['submitted']): ?>
                                <span class="badge text-bg-success">Evaluation submitted</span>
                            <?php else: ?>
                                <span class="badge text-bg-warning">Not yet evaluated</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($teacher['submitted']): ?>
                            <?php
                            $ownEvaluation = null;
                            foreach ($submittedEvaluations as $evaluation) {
                                if ((int) $evaluation['teacher_id'] === (int) $teacher['teacher_id']) {
                                    $ownEvaluation = $evaluation;
                                    break;
                                }
                            }
                            ?>
                            <div class="card-body">
                                <?php $ownTotal = array_sum($ownEvaluation['scores']); ?>
                                <p class="small text-muted">Submitted <?= esc(date('M j, Y g:i A', strtotime($ownEvaluation['submitted_at']))) ?>. Your ratings are shown below; submissions are locked.</p>
                                <div class="teacher-evaluation-summary mb-3">
                                    <span><strong>Total Criteria Rated:</strong> <?= count($criteria) ?> / 22</span>
                                    <span><strong>Total Rating Score:</strong> <?= $ownTotal ?> / 110</span>
                                    <span><strong>Average Rating:</strong> <?= number_format($ownTotal / count($criteria), 2) ?> / 5</span>
                                </div>
                                <?php foreach ($sections as $section => $sectionCriteria): ?>
                                    <h3 class="h6 teacher-evaluation-section-title"><?= esc($section) ?></h3>
                                    <div class="teacher-evaluation-own-scores mb-3">
                                        <?php foreach ($sectionCriteria as $key => $label): ?>
                                            <div><span><?= esc($label) ?></span><strong><?= esc($ownEvaluation['scores'][$key] ?? '—') ?> / 5</strong></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                                <h3 class="h6 teacher-evaluation-section-title">Comments and Suggestions</h3>
                                <?php foreach ($commentFields as $field => $label): ?>
                                    <div class="teacher-evaluation-comment">
                                        <strong><?= esc($label) ?></strong>
                                        <p><?= nl2br(esc($ownEvaluation[$field] ?? 'No comment provided.')) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="card-body">
                                <p class="small text-muted">Select one rating for each statement. All 22 criteria must be rated.</p>
                                <form method="POST" action="<?= site_url('student/teacher-evaluations') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="teacher_id" value="<?= (int) $teacher['teacher_id'] ?>">
                                    <div class="teacher-evaluation-rating-head">
                                        <div class="teacher-evaluation-rating-scale" aria-hidden="true">
                                            <?php for ($score = 1; $score <= 5; $score++): ?><span><?= $score ?></span><?php endfor; ?>
                                        </div>
                                        <span>Evaluation Criteria</span>
                                    </div>
                                    <?php foreach ($sections as $section => $sectionCriteria): ?>
                                        <section class="teacher-evaluation-section">
                                            <h3><?= esc($section) ?></h3>
                                            <?php foreach ($sectionCriteria as $key => $label): ?>
                                                <div class="teacher-evaluation-row" role="radiogroup" aria-label="<?= esc($label) ?>" aria-required="true">
                                                    <p class="teacher-evaluation-criterion">
                                                        <?= esc($label) ?><span class="teacher-evaluation-required" aria-hidden="true">*</span>
                                                    </p>
                                                    <div class="teacher-evaluation-options">
                                                        <?php for ($score = 1; $score <= 5; $score++): ?>
                                                            <label>
                                                                <input type="radio" name="scores[<?= esc($key) ?>]" value="<?= $score ?>" aria-label="<?= esc($label) ?>: rating <?= $score ?>" required>
                                                                <span aria-hidden="true"></span>
                                                            </label>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </section>
                                    <?php endforeach; ?>

                                    <section class="teacher-evaluation-section mt-4">
                                        <h3>Comments and Suggestions</h3>
                                        <?php foreach ($commentFields as $field => $label): ?>
                                            <div class="mb-3">
                                                <label for="evaluation-<?= esc($field) ?>" class="form-label fw-semibold"><?= esc($label) ?></label>
                                                <textarea id="evaluation-<?= esc($field) ?>" name="<?= esc($field) ?>" class="form-control" rows="3" maxlength="5000" placeholder="Your comments are optional."></textarea>
                                            </div>
                                        <?php endforeach; ?>
                                    </section>
                                    <p class="small text-muted">Thank you for completing this evaluation. Your feedback helps improve the quality of teaching and learning.</p>
                                    <button type="submit" class="btn btn-university mt-3">Submit Evaluation</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?= view('layouts/footer') ?>

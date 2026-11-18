<?php

use Illuminate\Support\Facades\Artisan;
use JeffersonGoncalves\UsPrivacyAssessments\Enums\AssessmentStatus;
use JeffersonGoncalves\UsPrivacyAssessments\Enums\RiskLevel;
use JeffersonGoncalves\UsPrivacyAssessments\Enums\Threat;
use JeffersonGoncalves\UsPrivacyAssessments\Facades\UsPrivacyAssessments;
use JeffersonGoncalves\UsPrivacyAssessments\Models\DataProtectionAssessment;
use JeffersonGoncalves\UsPrivacyAssessments\Tests\Fixtures\Applicant;
use JeffersonGoncalves\UsPrivacyAssessments\Tests\Fixtures\Customer;
use JeffersonGoncalves\UsPrivacyAssessments\Tests\Fixtures\Patient;
use JeffersonGoncalves\UsPrivacyNotices\Models\ProcessingActivity;

function activity(string $name, array $models): ProcessingActivity
{
    return ProcessingActivity::create(['name' => $name, 'purpose' => $name, 'models' => $models]);
}

function assessment(?ProcessingActivity $activity = null): DataProtectionAssessment
{
    return DataProtectionAssessment::create([
        'title' => 'Telemedicine',
        'processing_activity_id' => $activity?->getKey(),
        'risks' => [
            ['title' => 'Diagnosis leak', 'threat' => 'data_disclosure', 'likelihood' => '2', 'impact' => '3', 'measures' => 'Encryption at rest', 'residual_likelihood' => '1', 'residual_impact' => '3'],
            ['title' => 'Re-identification in reports', 'threat' => 'identifying', 'likelihood' => 1, 'impact' => 2],
        ],
    ]);
}

it('scores risks by likelihood and impact, before and after measures', function () {
    $risks = assessment()->scoredRisks();

    expect($risks[0])
        ->threat->toBe(Threat::DataDisclosure)
        ->level->toBe(RiskLevel::High)
        ->residual_level->toBe(RiskLevel::Medium)
        ->and($risks[1]['residual_level'])->toBe(RiskLevel::Low)
        ->and(assessment()->highestRisk(residual: false))->toBe(RiskLevel::High)
        ->and(assessment()->highestRisk())->toBe(RiskLevel::Medium)
        ->and(RiskLevel::score(3, 3))->toBe(RiskLevel::High)
        ->and(fn () => RiskLevel::score(4, 1))->toThrow(InvalidArgumentException::class);
});

it('approves, schedules a review and returns to draft when edited', function () {
    $dpia = assessment()->approve('Privacy lead');

    expect($dpia->status)->toBe(AssessmentStatus::Approved)
        ->and($dpia->next_review_at->toDateString())->toBe(now()->addYear()->toDateString());

    $this->travel(13)->months();

    expect($dpia->isReviewDue())->toBeTrue()
        ->and(DataProtectionAssessment::query()->scopes('dueForReview')->count())->toBe(1);

    $dpia->update(['necessity' => 'Only the data needed for the appointment']);

    expect($dpia->fresh())
        ->status->toBe(AssessmentStatus::Draft)
        ->approved_by->toBeNull()
        ->isReviewDue()->toBeFalse();
});

it('needs risks before approval', function () {
    DataProtectionAssessment::create(['title' => 'Empty'])->approve('DPO');
})->throws(LogicException::class, 'at least one risk');

it('lists activities that may need an assessment', function () {
    $health = activity('Telemedicine', [Patient::class]);
    activity('Billing', [Customer::class]);
    activity('Ad targeting', [Applicant::class]);

    expect(collect(UsPrivacyAssessments::candidates())->map(fn (array $c) => [$c['activity']->name, $c['reasons']])->all())->toBe([
        ['Ad targeting', ['targeted_advertising', 'sale']],
        ['Telemedicine', ['sensitive_data']],
    ]);

    assessment($health);

    expect(UsPrivacyAssessments::candidates())->toHaveCount(1);
});

it('reports assessments and candidates', function () {
    assessment(activity('Telemedicine', [Patient::class]))->approve('DPO');
    activity('Ad targeting', [Applicant::class]);

    Artisan::call('us-privacy:assessments');
    expect(Artisan::output())->toContain('Telemedicine')->toContain('high (medium)')->toContain('No data protection assessment for [Ad targeting]: targeted_advertising, sale');

    Artisan::call('us-privacy:assessments', ['--json' => true]);
    expect(json_decode(Artisan::output(), true)[0])
        ->processing_activity->toBe('Telemedicine')
        ->highest_residual_risk->toBe('medium')
        ->risks->{0}->threat->toBe('data_disclosure');
});

it('weighs the benefits in the export', function () {
    $assessment = DataProtectionAssessment::create(['title' => 'Ad targeting', 'benefits' => 'Relevant offers', 'risks' => [['title' => 'Profiling', 'likelihood' => 2, 'impact' => 2]]]);

    expect(UsPrivacyAssessments::export($assessment)['benefits'])->toBe('Relevant offers');
});

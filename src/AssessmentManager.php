<?php

namespace JeffersonGoncalves\UsPrivacyAssessments;

use JeffersonGoncalves\UsPrivacy\Enums\DataCategory;
use JeffersonGoncalves\UsPrivacy\UsPrivacy;
use JeffersonGoncalves\UsPrivacyAssessments\Models\DataProtectionAssessment;
use JeffersonGoncalves\UsPrivacyNotices\Models\ProcessingActivity;

class AssessmentManager
{
    public function __construct(protected UsPrivacy $privacy) {}

    /**
     * Processing activities with no assessment that the state laws require one for: targeted
     * advertising, sale of personal data, profiling, and processing of sensitive data. Other
     * processing with a heightened risk of harm still needs your judgment.
     *
     * @return list<array{activity: ProcessingActivity, reasons: list<string>}>
     */
    public function candidates(): array
    {
        $inventory = collect($this->privacy->inventory());
        $models = fn (callable $filter): array => $inventory->filter($filter)->pluck('model')->unique()->all();
        $triggers = [
            'targeted_advertising' => $models(fn (array $row): bool => $row['targeted_advertising']),
            'sale' => $models(fn (array $row): bool => $row['sold']),
            'profiling' => $models(fn (array $row): bool => $row['profiling']),
            'sensitive_data' => $models(fn (array $row): bool => $row['category'] === DataCategory::Sensitive->value),
        ];

        $assessed = DataProtectionAssessment::query()->whereNotNull('processing_activity_id')->pluck('processing_activity_id')->all();

        $candidates = [];

        foreach (ProcessingActivity::query()->whereNotIn('id', $assessed)->orderBy('name')->get() as $activity) {
            $reasons = array_keys(array_filter($triggers, fn (array $triggerModels): bool => array_intersect($activity->models ?? [], $triggerModels) !== []));

            if ($reasons !== []) {
                $candidates[] = ['activity' => $activity, 'reasons' => $reasons];
            }
        }

        return $candidates;
    }

    /**
     * @return array<string, mixed>
     */
    public function export(DataProtectionAssessment $assessment): array
    {
        return [
            'title' => $assessment->title,
            'processing_activity' => $assessment->processingActivity?->name,
            'description' => $assessment->description,
            'benefits' => $assessment->benefits,
            'necessity' => $assessment->necessity,
            'risks' => array_map(fn (array $risk): array => [
                ...$risk,
                'threat' => $risk['threat']?->value,
                'level' => $risk['level']->value,
                'residual_level' => $risk['residual_level']->value,
            ], $assessment->scoredRisks()),
            'highest_residual_risk' => $assessment->highestRisk()?->value,
            'status' => $assessment->status->value,
            'approved_at' => $assessment->approved_at?->toIso8601String(),
            'approved_by' => $assessment->approved_by,
            'next_review_at' => $assessment->next_review_at?->toIso8601String(),
        ];
    }
}

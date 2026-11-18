<?php

namespace JeffersonGoncalves\UsPrivacyAssessments\Commands;

use Illuminate\Console\Command;
use JeffersonGoncalves\UsPrivacyAssessments\AssessmentManager;
use JeffersonGoncalves\UsPrivacyAssessments\Models\DataProtectionAssessment;

class AssessmentsCommand extends Command
{
    protected $signature = 'us-privacy:assessments {--json : Output every assessment as JSON}';

    protected $description = 'List the data protection assessments and the processing activities that may still need one';

    public function handle(AssessmentManager $manager): int
    {
        $assessments = DataProtectionAssessment::query()->with('processingActivity')->orderBy('title')->get();

        if ($this->option('json')) {
            $this->line((string) json_encode($assessments->map(fn (DataProtectionAssessment $a): array => $manager->export($a))->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        if ($assessments->isNotEmpty()) {
            $this->table(
                ['#', 'Title', 'Activity', 'Status', 'Risks', 'Highest (residual)', 'Next review'],
                $assessments->map(fn (DataProtectionAssessment $a): array => [
                    $a->getKey(),
                    $a->title,
                    $a->processingActivity->name ?? '-',
                    $a->status->value.($a->isReviewDue() ? ' (review due)' : ''),
                    count($a->scoredRisks()),
                    ($a->highestRisk(false)->value ?? '-').' ('.($a->highestRisk()->value ?? '-').')',
                    $a->next_review_at?->toDateString() ?? '-',
                ])->all(),
            );
        }

        $candidates = $manager->candidates();

        foreach ($candidates as $candidate) {
            $this->warn("No data protection assessment for [{$candidate['activity']->name}]: ".implode(', ', $candidate['reasons']));
        }

        if ($assessments->isEmpty() && $candidates === []) {
            $this->info('No assessments, and no processing activity that obviously needs one.');
        }

        return self::SUCCESS;
    }
}

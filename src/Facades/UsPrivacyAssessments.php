<?php

namespace JeffersonGoncalves\UsPrivacyAssessments\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\UsPrivacyAssessments\AssessmentManager;

/**
 * @method static list<array{activity: \JeffersonGoncalves\UsPrivacyNotices\Models\ProcessingActivity, reasons: list<string>}> candidates()
 * @method static array<string, mixed> export(\JeffersonGoncalves\UsPrivacyAssessments\Models\DataProtectionAssessment $assessment)
 *
 * @see AssessmentManager
 */
class UsPrivacyAssessments extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AssessmentManager::class;
    }
}

<?php

namespace JeffersonGoncalves\UsPrivacyAssessments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\UsPrivacyAssessments\UsPrivacyAssessments
 */
class UsPrivacyAssessments extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-us-privacy-assessments';
    }
}

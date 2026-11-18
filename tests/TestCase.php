<?php

namespace JeffersonGoncalves\UsPrivacyAssessments\Tests;

use JeffersonGoncalves\UsPrivacyAssessments\UsPrivacyAssessmentsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            UsPrivacyAssessmentsServiceProvider::class,
        ];
    }
}

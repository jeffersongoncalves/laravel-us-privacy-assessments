<?php

namespace JeffersonGoncalves\UsPrivacyAssessments\Tests;

use JeffersonGoncalves\PestPluginDatabaseMigrations\LoadsPackageMigrations;
use JeffersonGoncalves\UsPrivacy\UsPrivacyServiceProvider;
use JeffersonGoncalves\UsPrivacyAssessments\Tests\Fixtures\Applicant;
use JeffersonGoncalves\UsPrivacyAssessments\Tests\Fixtures\Customer;
use JeffersonGoncalves\UsPrivacyAssessments\Tests\Fixtures\Patient;
use JeffersonGoncalves\UsPrivacyAssessments\UsPrivacyAssessmentsServiceProvider;
use JeffersonGoncalves\UsPrivacyNotices\UsPrivacyNoticesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use LoadsPackageMigrations;

    protected function getPackageProviders($app): array
    {
        return [
            UsPrivacyServiceProvider::class,
            UsPrivacyNoticesServiceProvider::class,
            UsPrivacyAssessmentsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('us-privacy.models', [Customer::class, Patient::class, Applicant::class]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadPackageMigrations(UsPrivacyNoticesServiceProvider::class);
        $this->loadPackageMigrations(UsPrivacyAssessmentsServiceProvider::class);
    }
}

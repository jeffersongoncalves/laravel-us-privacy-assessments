<?php

namespace JeffersonGoncalves\UsPrivacyAssessments;

use JeffersonGoncalves\UsPrivacyAssessments\Commands\AssessmentsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UsPrivacyAssessmentsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('us-privacy-assessments')
            ->hasConfigFile()
            ->hasMigrations(['create_us_privacy_assessments_table'])
            ->hasCommand(AssessmentsCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(AssessmentManager::class);
    }
}

<?php

namespace JeffersonGoncalves\UsPrivacyAssessments;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UsPrivacyAssessmentsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-us-privacy-assessments')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}

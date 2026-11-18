<?php

namespace JeffersonGoncalves\UsPrivacyAssessments\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\UsPrivacy\Attributes\PersonalData;
use JeffersonGoncalves\UsPrivacy\Concerns\InteractsWithPersonalData;
use JeffersonGoncalves\UsPrivacy\Contracts\HasPersonalData;
use JeffersonGoncalves\UsPrivacy\Enums\DataCategory;

#[PersonalData('name')]
#[PersonalData('diagnosis', category: DataCategory::Sensitive)]
class Patient extends Model implements HasPersonalData
{
    use InteractsWithPersonalData;

    protected $table = 'patients';
}

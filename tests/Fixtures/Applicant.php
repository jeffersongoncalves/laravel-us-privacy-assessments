<?php

namespace JeffersonGoncalves\UsPrivacyAssessments\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\UsPrivacy\Attributes\PersonalData;
use JeffersonGoncalves\UsPrivacy\Concerns\InteractsWithPersonalData;
use JeffersonGoncalves\UsPrivacy\Contracts\HasPersonalData;

#[PersonalData('name')]
#[PersonalData('browsing', sold: true, targetedAdvertising: true)]
class Applicant extends Model implements HasPersonalData
{
    use InteractsWithPersonalData;

    protected $table = 'applicants';
}

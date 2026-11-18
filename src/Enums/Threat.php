<?php

namespace JeffersonGoncalves\UsPrivacyAssessments\Enums;

/**
 * LINDDUN privacy threat categories.
 */
enum Threat: string
{
    case Linking = 'linking';
    case Identifying = 'identifying';
    case NonRepudiation = 'non_repudiation';
    case Detecting = 'detecting';
    case DataDisclosure = 'data_disclosure';
    case Unawareness = 'unawareness';
    case NonCompliance = 'non_compliance';
}

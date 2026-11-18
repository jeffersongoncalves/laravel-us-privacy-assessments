<?php

return [

    'table' => 'us_privacy_assessments',

    /*
     * Review the assessment periodically and whenever the processing changes materially; Colorado
     * expects a yearly review of assessments of profiling. Keep each one at least 3 years after the
     * processing ends, and hand it to the attorney general on request. CarbonInterval string.
     */
    'review_every' => '1 year',

];

<?php

return [


    /*
    * Here you will find all the settings used by this application.
    */
    'institution_name' => env('INST_NAME', 'My Institution'),
    'alma_api_key' => env('ALMA_API_KEY', NULL),
    'shared_reports_path' => env('SHARED_REPORTS_PATH', NULL),
    'admin_shared_reports_path' => env('ADMIN_SHARED_REPORTS_PATH', NULL),
    'homepage_acquisitions_list' => env('HOMEPAGE_ACQUISITIONS_LIST', NULL),
];

<?php

return [

    /*
    | When non-empty, only requests to central domains (see config/tenancy.php)
    | may proceed after unlocking via /site-access. Leave empty to disable.
    */
    'access_password' => env('SITE_ACCESS_PASSWORD'),

];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Host User model
    |--------------------------------------------------------------------------
    | The blog package relates posts to the host application's User model as the
    | author. Leave null to resolve it from the auth config.
    */
    'user_model' => env('BLOG_USER_MODEL'),
];

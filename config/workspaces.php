<?php

declare(strict_types=1);

use App\Http\Middleware\AuthenticateSessionMiddleware;
use App\Utilities\Workspaces\WorkspaceFeatures;

return [

    /*
     |--------------------------------------------------------------------------
     | Workspaces Route Middleware
     |--------------------------------------------------------------------------
     |
     | Here you may specify which middleware Workspaces will assign to the routes
     | that it registers with the application. When necessary, you may modify
     | these middleware; however, this default value is usually sufficient.
     |
     */

    'middleware' => ['web'],

    'auth_session' => AuthenticateSessionMiddleware::class,

    /*
    |--------------------------------------------------------------------------
    | Workspaces Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify the authentication guard Workspaces will use while
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

    'guard' => 'sanctum',

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Some of Workspaces features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features, or you can even remove all of these if you need to.
    |
    */

    'features' => [
        WorkspaceFeatures::termsAndPrivacyPolicy(),
        WorkspaceFeatures::profilePhotos(),
        WorkspaceFeatures::api(),
        WorkspaceFeatures::workspaces(['invitations' => true]),
        WorkspaceFeatures::accountDeletion(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Profile Photo Disk
    |--------------------------------------------------------------------------
    |
    | This configuration value determines the default disk that will be used
    | when storing profile photos for your application's users. Typically,
    | this will be the "public" disk, but you may adjust this if needed.
    |
    */

    'profile_photo_disk' => env('WORKSPACES_PROFILE_PHOTO_DISK', 'public'),

];

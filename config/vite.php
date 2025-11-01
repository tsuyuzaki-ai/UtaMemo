<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vite Development Server URL
    |--------------------------------------------------------------------------
    |
    | When running `npm run dev`, Vite will start a development server on a
    | local host. This value sets which host that is. The host will be used
    | when checking if Vite is running, and for generating asset URLs.
    |
    */

    'host' => env('VITE_HOST', 'localhost'),

    /*
    |--------------------------------------------------------------------------
    | Development Server Port
    |--------------------------------------------------------------------------
    |
    | The port that the Vite development server is running on.
    |
    */

    'port' => env('VITE_PORT', 5173),

    /*
    |--------------------------------------------------------------------------
    | Build Directory
    |--------------------------------------------------------------------------
    |
    | The directory where Vite will store its build artifacts. The build
    | directory should be included in your .gitignore file.
    |
    */

    'build_path' => 'build',

    /*
    |--------------------------------------------------------------------------
    | Hot Module Replacement
    |--------------------------------------------------------------------------
    |
    | Enables Hot Module Replacement (HMR) in development mode. When
    | enabled, Vite will inject the HMR client into your application
    | allowing for instant updates without a full page reload.
    |
    */

    'hmr' => env('VITE_HMR', false),

];


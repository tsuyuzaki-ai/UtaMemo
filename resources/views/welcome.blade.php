<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UtaMemo</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    @if(app()->environment('local'))
        @vite('resources/css/app.css')
        @vite('resources/css/style.css')
        @vite('resources/js/app.js')
    @else
        @php
            $manifestPath = public_path('build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifestContent = file_get_contents($manifestPath);
                $manifest = json_decode($manifestContent, true);
                if ($manifest && json_last_error() === JSON_ERROR_NONE) {
                    // CSS files
                    if (isset($manifest['resources/css/app.css']['file'])) {
                        echo '<link rel="stylesheet" href="' . asset('build/' . $manifest['resources/css/app.css']['file']) . '">';
                    }
                    if (isset($manifest['resources/css/style.css']['file'])) {
                        echo '<link rel="stylesheet" href="' . asset('build/' . $manifest['resources/css/style.css']['file']) . '">';
                    }
                    // JS file
                    if (isset($manifest['resources/js/app.js']['file'])) {
                        echo '<script type="module" src="' . asset('build/' . $manifest['resources/js/app.js']['file']) . '"></script>';
                    }
                    // Additional CSS from JS bundle
                    if (isset($manifest['resources/js/app.js']['css'])) {
                        foreach ($manifest['resources/js/app.js']['css'] as $css) {
                            echo '<link rel="stylesheet" href="' . asset('build/' . $css) . '">';
                        }
                    }
                }
            }
        @endphp
    @endif
</head>
<body>
    <div id="app"></div>
</body>
</html>

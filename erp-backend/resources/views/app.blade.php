<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'ERP') }}</title>
    <!-- <link rel="shortcut icon" href="/assets/media/logos/favicon.ico" /> -->
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!-- <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" /> -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!-- <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" /> -->
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
    <div id="app">
        <router-view></router-view>
    </div>
    <!-- <script src="/assets/plugins/global/plugins.bundle.js"></script> -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <!-- <script src="/assets/js/scripts.bundle.js"></script> -->
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
</body>
</html>

<?php

// Language switcher
App::route('POST', '/lang', 'LangController', 'switch');

// Authentication routes
App::route('GET', '/login', 'AuthController', 'showLogin');
App::route('POST', '/login', 'AuthController', 'login');
App::route('GET', '/logout', 'AuthController', 'logout');
App::route('GET', '/change-password', 'AuthController', 'showChangePassword');
App::route('POST', '/change-password', 'AuthController', 'changePassword');

// Reports routes
App::route('GET', '/reports', 'ReportController', 'index');
App::route('GET', '/reports/upload', 'ReportController', 'showUpload');
App::route('POST', '/reports/upload', 'ReportController', 'upload');

// Dashboard (protected)
App::route('GET', '/', 'DashboardController', 'index');
App::route('GET', '/dashboard', 'DashboardController', 'index');
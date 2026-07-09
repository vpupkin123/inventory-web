<?php

// Authentication routes
App::route('GET', '/login', 'AuthController', 'showLogin');
App::route('POST', '/login', 'AuthController', 'login');
App::route('GET', '/logout', 'AuthController', 'logout');
App::route('GET', '/change-password', 'AuthController', 'showChangePassword');
App::route('POST', '/change-password', 'AuthController', 'changePassword');

// Dashboard (protected)
App::route('GET', '/', 'DashboardController', 'index');
App::route('GET', '/dashboard', 'DashboardController', 'index');
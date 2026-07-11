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

// Processing routes
App::route('GET', '/processing', 'ProcessingController', 'index');
App::route('POST', '/processing/process', 'ProcessingController', 'process');

// Computers & Transfers routes
App::route('GET', '/computers', 'ComputerController', 'index');
App::route('GET', '/computer', 'ComputerController', 'show');
App::route('GET', '/computer/transfer', 'ComputerController', 'showTransfer');
App::route('POST', '/computer/transfer', 'ComputerController', 'transfer');
App::route('GET', '/computers/export', 'ComputerController', 'export');
App::route('GET', '/transfers/export', 'TransferController', 'export');

App::route('GET', '/transfers', 'TransferController', 'index');

// User Management routes (Admin only)
App::route('GET', '/users', 'UserController', 'index');
App::route('GET', '/users/create', 'UserController', 'create');
App::route('POST', '/users/store', 'UserController', 'store');
App::route('GET', '/users/edit', 'UserController', 'edit');
App::route('POST', '/users/update', 'UserController', 'update');
App::route('GET', '/users/block', 'UserController', 'toggleBlock');
App::route('GET', '/users/delete', 'UserController', 'delete');

// AJAX endpoints
App::route('GET', '/api/check-login', 'ApiController', 'checkLogin');
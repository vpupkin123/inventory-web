<?php

class DashboardController
{
    public function index(): void
    {
        Auth::requireAuth();

        $user = Auth::user();

        View::render('dashboard/index', [
            'user' => $user
        ]);
    }
}
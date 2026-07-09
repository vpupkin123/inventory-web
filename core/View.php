<?php

class View
{
    /**
     * Render a view with layout
     */
    public static function render(string $viewName, array $data = []): void
    {
        // Extract data to variables
        extract($data);

        // Start output buffering for content
        ob_start();

        // Load view file
        $viewFile = ROOT_PATH . "/views/{$viewName}.php";
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: {$viewFile}");
        }

        require $viewFile;

        // Get content
        $content = ob_get_clean();

        // Load layout
        $layoutFile = ROOT_PATH . '/views/layouts/main.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            // No layout, just output content
            echo $content;
        }
    }

    /**
     * Render partial view (without layout)
     */
    public static function partial(string $viewName, array $data = []): void
    {
        extract($data);
        $viewFile = ROOT_PATH . "/views/{$viewName}.php";
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: {$viewFile}");
        }
        require $viewFile;
    }
}
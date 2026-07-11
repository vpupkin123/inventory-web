<?php

class App
{
    private static array $routes = [];

    /**
     * Register a route
     */
    public static function route(string $method, string $path, string $controller, string $action): void
    {
        self::$routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    /**
     * Run the application
     */
    public static function run(): void
    {
        session_start();

        // Initialize language system
        Lang::init();

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($requestUri, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Remove trailing slash (except for root)
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        // Find matching route
        foreach (self::$routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $controllerName = $route['controller'];
                $actionName = $route['action'];

                // Load controller
                $controllerFile = ROOT_PATH . "/controllers/{$controllerName}.php";
                if (!file_exists($controllerFile)) {
                    self::show404("Controller file not found: {$controllerFile}");
                    return;
                }

                require_once $controllerFile;

                // Create controller instance and call action
                if (!class_exists($controllerName)) {
                    self::show404("Controller class not found: {$controllerName}");
                    return;
                }

                $controller = new $controllerName();

                if (!method_exists($controller, $actionName)) {
                    self::show404("Action not found: {$controllerName}::{$actionName}");
                    return;
                }

                $controller->$actionName();
                return;
            }
        }

        // No route matched
        self::show404();
    }

    /**
     * Show 404 page
     */
    private static function show404(string $message = ''): void
    {
        http_response_code(404);
        echo "<h1>" . Lang::t('common.error_404') . "</h1>";
        if ($message) {
            echo "<p>" . htmlspecialchars($message) . "</p>";
        }
    }
}
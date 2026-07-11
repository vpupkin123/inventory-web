<?php

class ApiController
{
    public function checkLogin(): void
    {
        header('Content-Type: application/json');
        
        $login = $_GET['login'] ?? '';
        $excludeId = (int)($_GET['exclude_id'] ?? 0);
        
        if (empty($login)) {
            echo json_encode(['available' => false, 'message' => 'Login is empty']);
            exit;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?" . ($excludeId > 0 ? " AND id != ?" : ""));
        
        if ($excludeId > 0) {
            $stmt->execute([$login, $excludeId]);
        } else {
            $stmt->execute([$login]);
        }
        
        $exists = $stmt->fetch();
        
        echo json_encode([
            'available' => !$exists,
            'message' => $exists ? 'Login already exists' : 'Login is available'
        ]);
        exit;
    }
}
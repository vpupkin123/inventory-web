<?php

class DashboardController
{
    public function index(): void
    {
        Auth::requireAuth();
        
        $pdo = Database::getConnection();
        $currentUser = Auth::user();
        
        // Get statistics
        $stats = [];
        
        // Total computers
        $stmt = $pdo->query("SELECT COUNT(*) FROM computers");
        $stats['total_computers'] = (int)$stmt->fetchColumn();
        
        // Computers on warehouse
        $warehouseId = Database::getWarehouseId();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM computers WHERE current_user_id = ?");
        $stmt->execute([$warehouseId]);
        $stats['warehouse_computers'] = (int)$stmt->fetchColumn();
        
        // Computers assigned to users
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM computers WHERE current_user_id != ?");
        $stmt->execute([$warehouseId]);
        $stats['assigned_computers'] = (int)$stmt->fetchColumn();
        
        // Unprocessed computers
        $stmt = $pdo->query("SELECT COUNT(*) FROM computers WHERE is_processed = 0");
        $stats['unprocessed'] = (int)$stmt->fetchColumn();
        
        // Total users (excluding warehouse)
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE login != 'warehouse'");
        $stats['total_users'] = (int)$stmt->fetchColumn();
        
        // Recent uploads
        $stmt = $pdo->query("
            SELECT pu.file_name, pu.uploaded_at, u.last_name, u.first_name
            FROM processed_uploads pu
            LEFT JOIN users u ON pu.uploaded_by = u.id
            ORDER BY pu.uploaded_at DESC
            LIMIT 5
        ");
        $stats['recent_uploads'] = $stmt->fetchAll();
        
        // Recent transfers
        $stmt = $pdo->query("
            SELECT t.transferred_at, c.computer_name, 
                   u_from.login as from_login, u_to.login as to_login
            FROM transfers t
            JOIN computers c ON t.computer_id = c.id
            LEFT JOIN users u_from ON t.from_user_id = u_from.id
            LEFT JOIN users u_to ON t.to_user_id = u_to.id
            ORDER BY t.transferred_at DESC
            LIMIT 5
        ");
        $stats['recent_transfers'] = $stmt->fetchAll();
        
        View::render('dashboard/index', [
            'user' => $currentUser,
            'stats' => $stats
        ]);
    }
}
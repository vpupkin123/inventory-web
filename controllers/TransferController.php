<?php

class TransferController
{
    public function index(): void
    {
        Auth::requireAuth();
        $pdo = Database::getConnection();

        $stmt = $pdo->query("
            SELECT 
                t.*, 
                c.computer_name, 
                c.serial_number,
                u_from.login as from_login,
                u_to.login as to_login,
                u_by.login as by_login
            FROM transfers t
            JOIN computers c ON t.computer_id = c.id
            LEFT JOIN users u_from ON t.from_user_id = u_from.id
            LEFT JOIN users u_to ON t.to_user_id = u_to.id
            LEFT JOIN users u_by ON t.transferred_by = u_by.id
            ORDER BY t.transferred_at DESC
        ");
        $transfers = $stmt->fetchAll();

        View::render('transfers/index', ['transfers' => $transfers]);
    }
}
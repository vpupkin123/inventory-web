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

        public function export(): void
    {
        Auth::requireAuth();
        $pdo = Database::getConnection();

        $stmt = $pdo->query("
            SELECT 
                t.transferred_at, 
                c.computer_name, 
                c.serial_number,
                u_from.login as from_login,
                u_to.login as to_login,
                u_by.login as by_login,
                t.comment
            FROM transfers t
            JOIN computers c ON t.computer_id = c.id
            LEFT JOIN users u_from ON t.from_user_id = u_from.id
            LEFT JOIN users u_to ON t.to_user_id = u_to.id
            LEFT JOIN users u_by ON t.transferred_by = u_by.id
            ORDER BY t.transferred_at DESC
        ");
        $transfers = $stmt->fetchAll();

        $headers = [
            Lang::t('history.date'),
            Lang::t('history.computer'),
            Lang::t('computer.serial'),
            Lang::t('history.from'),
            Lang::t('history.to'),
            Lang::t('history.by'),
            Lang::t('history.comment')
        ];

        $rows = [];
        foreach ($transfers as $t) {
            $rows[] = [
                $t['transferred_at'],
                $t['computer_name'],
                $t['serial_number'] ?: 'N/A',
                $t['from_login'] ?: Lang::t('history.warehouse'),
                $t['to_login'] ?: Lang::t('history.warehouse'),
                $t['by_login'],
                $t['comment']
            ];
        }

        Export::toExcel('transfers_' . date('Y-m-d') . '.xls', $headers, $rows);
    }
}
<?php

/**
 * Simple HTML-to-Excel exporter.
 * Generates .xls files that Excel opens natively. No external libraries required.
 */
class Export
{
    public static function toExcel(string $filename, array $headers, array $rows): void
    {
        // Prevent any output buffering issues
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Headers to force Excel download
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        // HTML structure with Excel namespaces
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="UTF-8"></head><body>';
        
        echo '<table border="1" style="border-collapse: collapse;">';
        
        // Header row
        echo '<tr style="background-color: #f2f2f2; font-weight: bold;">';
        foreach ($headers as $header) {
            echo '<th style="padding: 5px;">' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr>';

        // Data rows
        foreach ($rows as $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                echo '<td style="padding: 5px;">' . htmlspecialchars((string)$cell) . '</td>';
            }
            echo '</tr>';
        }

        echo '</table></body></html>';
        exit;
    }
}
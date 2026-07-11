<?php

/**
 * JSON report parser
 * Handles the real structure from the client app:
 * { User, System, Hardware: { Computer, Motherboard, Cpu, Ram, Storage } }
 * Automatically fixes Windows BOM and trailing whitespace issues.
 */
class JsonParser
{
    /**
     * Values that mean "serial number unknown"
     */
    private static array $unknownSerials = [
        'n/a',
        'to be filled by o.e.m.',
        'default string',
        'not specified',
        'unknown',
        '',
    ];

    /**
     * Parse JSON file and return array of computers
     * Supports both single object and array of objects
     */
    public static function parse(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new RuntimeException("Cannot read file: $filePath");
        }

        // === Fix WINDOWS ===
        // 1. Deleting UTF-8 BOM
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }
        
        // 2. Deleting spaces
        $content = trim($content);
        // ====================================

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid JSON format: " . json_last_error_msg());
        }

        // Support both single object and array
        if (isset($data['Hardware'])) {
            $items = [$data];
        } elseif (is_array($data)) {
            $items = $data;
        } else {
            throw new RuntimeException("Invalid JSON structure: expected object with Hardware or array of objects");
        }

        $computers = [];

        foreach ($items as $item) {
            $parsed = self::parseOne($item);
            if ($parsed !== null) {
                $computers[] = $parsed;
            }
        }

        return $computers;
    }

    /**
     * Parse one computer entry
     */
    private static function parseOne(array $item): ?array
    {
        $computer = $item['Hardware']['Computer'] ?? [];
        $motherboard = $item['Hardware']['Motherboard'] ?? [];
        $cpu = $item['Hardware']['Cpu'] ?? [];
        $ram = $item['Hardware']['Ram'] ?? [];
        $storageList = $item['Hardware']['Storage'] ?? [];
        $os = $item['System']['Os'] ?? [];
        $network = $item['System']['Network'] ?? [];
        $user = $item['User'] ?? [];

        // Determine serial number
        $pcSerial = trim($computer['SerialNumber'] ?? '');
        $mbSerial = trim($motherboard['SerialNumber'] ?? '');
        $serialNumber = null;
        $compositeKey = null;

        if (!self::isUnknownSerial($pcSerial)) {
            $serialNumber = $pcSerial;
        } elseif (!self::isUnknownSerial($mbSerial)) {
            // Use motherboard serial as fallback
            $serialNumber = $mbSerial;
        } else {
            // Generate composite key from stable hardware identifiers
            $compositeString = ($motherboard['Model'] ?? '') . '|' .
                               ($cpu['Name'] ?? '') . '|' .
                               ($ram['TotalGb'] ?? 0);
            $compositeKey = hash('sha256', $compositeString);
        }

        // Build RAM details string
        $ramDetails = '';
        if (!empty($ram['Modules']) && is_array($ram['Modules'])) {
            $parts = [];
            foreach ($ram['Modules'] as $module) {
                $mfr = $module['Manufacturer'] ?? 'Unknown';
                $capacity = $module['CapacityGb'] ?? 0;
                $speed = $module['SpeedMhz'] ?? 0;
                $type = $module['Type'] ?? '';
                $parts[] = "{$capacity}GB {$type} {$speed}MHz";
            }
            $ramDetails = implode(', ', $parts);
        }

        // Build storage info string (filter out USB/removable drives)
        $storageParts = [];
        foreach ($storageList as $disk) {
            $mediaType = strtolower($disk['MediaType'] ?? '');
            $interfaceType = strtolower($disk['InterfaceType'] ?? '');

            // Filter USB and removable drives
            if (str_contains($mediaType, 'removable') || 
                str_contains($mediaType, 'external') ||
                $interfaceType === 'usb') {
                continue;
            }

            $model = $disk['Model'] ?? 'Unknown';
            $size = $disk['SizeGb'] ?? 0;
            $storageParts[] = "{$model} ({$size}GB)";
        }
        $storageInfo = implode(', ', $storageParts);

        return [
            'serial_number'   => $serialNumber,
            'composite_key'   => $compositeKey,
            'manufacturer'    => $computer['Manufacturer'] ?? '',
            'model'           => $computer['Model'] ?? '',
            'motherboard'     => trim(($motherboard['Manufacturer'] ?? '') . ' ' . ($motherboard['Model'] ?? '')),
            'cpu_name'        => trim($cpu['Name'] ?? ''),
            'cpu_cores'       => (int)($cpu['Cores'] ?? 0),
            'cpu_threads'     => (int)($cpu['LogicalProcessors'] ?? 0),
            'ram_total_gb'    => (float)($ram['TotalGb'] ?? 0),
            'ram_details'     => $ramDetails,
            'storage_info'    => $storageInfo,
            'os_caption'      => $os['Caption'] ?? '',
            'os_build'        => $os['BuildNumber'] ?? '',
            'ip_address'      => $network['IpAddress'] ?? '',
            'computer_name'   => $item['System']['ComputerName'] ?? '',
            'reported_by'     => $user['FullName'] ?? '',
        ];
    }

    /**
     * Check if serial number value means "unknown"
     */
    private static function isUnknownSerial(string $serial): bool
    {
        $lower = strtolower(trim($serial));
        return in_array($lower, self::$unknownSerials, true);
    }
}
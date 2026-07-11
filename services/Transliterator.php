<?php

/**
 * Transliteration service according to GOST R 7.0.34-2014
 */
class Transliterator
{
    private static array $map = [
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
        'Й' => 'I', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => 'Ie', 'Ы' => 'Y', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Iu', 'Я' => 'Ia'
    ];

    /**
     * Transliterate Cyrillic text to Latin according to GOST R 7.0.34-2014
     */
    public static function transliterate(string $text): string
    {
        $result = '';
        $length = mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $upperChar = mb_strtoupper($char, 'UTF-8');

            if (isset(self::$map[$upperChar])) {
                $translit = self::$map[$upperChar];
                if ($char === mb_strtolower($char, 'UTF-8') && $translit !== '') {
                    $translit = mb_strtolower($translit, 'UTF-8');
                }
                $result .= $translit;
            } else {
                $result .= $char;
            }
        }

        return $result;
    }

    /**
     * Generate login from FIO (Lastname Initials)
     * Example: Иванов Иван Иванович -> ivanov.ii
     */
    public static function generateLogin(string $lastName, string $firstName, string $middleName = ''): string
    {
        $login = mb_strtolower(self::transliterate($lastName), 'UTF-8');
        
        $initials = '';
        if (!empty($firstName)) {
            $initials .= mb_strtolower(mb_substr(self::transliterate($firstName), 0, 1, 'UTF-8'), 'UTF-8');
        }
        if (!empty($middleName)) {
            $initials .= mb_strtolower(mb_substr(self::transliterate($middleName), 0, 1, 'UTF-8'), 'UTF-8');
        }

        if (!empty($initials)) {
            $login .= '.' . $initials;
        }

        $login = preg_replace('/[^a-z0-9.]/', '', $login);

        return $login;
    }
}
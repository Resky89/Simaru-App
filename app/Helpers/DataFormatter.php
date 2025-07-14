<?php

namespace App\Helpers;

class DataFormatter
{
    /**
     * Format error messages dari response API
     *
     * @param mixed $errorData
     * @return string
     */
    public static function formatErrorMessage($errorData)
    {
        if (empty($errorData)) {
            return 'Terjadi kesalahan pada server';
        }

        $errorMessage = '';

        if (is_array($errorData)) {
            foreach ($errorData as $field => $messages) {
                if (is_array($messages)) {
                    $errorMessage .= implode(', ', $messages) . '; ';
                } else {
                    $errorMessage .= $messages . '; ';
                }
            }
        } else {
            $errorMessage = $errorData;
        }

        return $errorMessage;
    }

    /**
     * Format data dari request untuk dikirim ke API
     *
     * @param \Illuminate\Http\Request $request
     * @param array $fields
     * @return array
     */
    public static function formatRequestData($request, $fields)
    {
        $data = [];

        foreach ($fields as $field => $options) {
            // Jika options adalah string, itu adalah tipe data
            if (is_string($options)) {
                $fieldName = $field;
                $type = $options;
                $required = false;
            } else {
                $fieldName = $field;
                $type = $options['type'] ?? 'string';
                $required = $options['required'] ?? false;
            }

            // Hanya tambahkan field yang ada di request atau yang required
            if ($request->has($fieldName) || $required) {
                $value = $request->input($fieldName);

                // Skip jika nilai kosong dan field tidak required
                if (empty($value) && !$required) {
                    continue;
                }

                // Konversi nilai sesuai tipe data
                switch ($type) {
                    case 'int':
                    case 'integer':
                        $data[$fieldName] = (int) $value;
                        break;
                    case 'float':
                    case 'double':
                        $data[$fieldName] = (float) $value;
                        break;
                    case 'bool':
                    case 'boolean':
                        $data[$fieldName] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'array':
                        $data[$fieldName] = is_array($value) ? $value : [$value];
                        break;
                    default:
                        $data[$fieldName] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Format tanggal dari format input ke format output yang diinginkan
     *
     * @param string|null $date
     * @param string $inputFormat
     * @param string $outputFormat
     * @return string|null
     */
    public static function formatDate($date, $inputFormat = 'Y-m-d', $outputFormat = 'd/m/Y')
    {
        if (empty($date)) {
            return null;
        }

        try {
            $dateObj = \DateTime::createFromFormat($inputFormat, $date);
            return $dateObj ? $dateObj->format($outputFormat) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format angka ke format uang (rupiah)
     *
     * @param float|int $amount
     * @param string $currencySymbol
     * @return string
     */
    public static function formatCurrency($amount, $currencySymbol = 'Rp')
    {
        if (!is_numeric($amount)) {
            return $currencySymbol . ' 0';
        }

        return $currencySymbol . ' ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format nomor telepon ke format yang konsisten
     *
     * @param string $phone
     * @return string
     */
    public static function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '';
        }

        // Hapus semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika awalan 0, ganti dengan +62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika tidak ada kode negara, tambahkan +62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return '+' . $phone;
    }

    /**
     * Format ukuran file ke format yang mudah dibaca
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatFileSize($bytes, $precision = 2)
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($bytes, 1024);
        $power = min(floor($base), count($units) - 1);

        return round(pow(1024, $base - $power), $precision) . ' ' . $units[$power];
    }

    /**
     * Membatasi panjang teks dengan ellipsis
     *
     * @param string $text
     * @param int $length
     * @param string $ellipsis
     * @return string
     */
    public static function truncateText($text, $length = 100, $ellipsis = '...')
    {
        if (empty($text)) {
            return '';
        }

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . $ellipsis;
    }

    /**
     * Generate random string
     *
     * @param int $length
     * @param bool $includeSpecialChars
     * @return string
     */
    public static function generateRandomString($length = 10, $includeSpecialChars = false)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ($includeSpecialChars) {
            $characters .= '!@#$%^&*()_-=+;:,.?';
        }

        $randomString = '';
        $charactersLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

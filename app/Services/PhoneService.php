<?php

namespace App\Services;

class PhoneService
{
    private array $countryCodes = [
        '+962' => 'Jordan',
        '+966' => 'Saudi Arabia',
        '+971' => 'UAE',
        '+20' => 'Egypt',
        '+961' => 'Lebanon',
        '+963' => 'Syria',
        '+964' => 'Iraq',
        '+965' => 'Kuwait',
        '+973' => 'Bahrain',
        '+974' => 'Qatar',
        '+968' => 'Oman',
        '+967' => 'Yemen',
        '+1' => 'USA/Canada',
        '+44' => 'UK',
    ];

    /**
     * Normalize phone number to E.164 format
     */
    public function normalizePhone(string $phone, ?string $defaultCountryCode = '+962'): ?string
    {
        // Remove all non-digit characters except +
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
        
        // If already in E.164 format (starts with +)
        if (str_starts_with($cleanPhone, '+')) {
            return $this->validateE164($cleanPhone) ? $cleanPhone : null;
        }
        
        // Handle local formats
        if ($defaultCountryCode === '+962') {
            return $this->normalizeJordanianPhone($cleanPhone);
        }
        
        // For other countries, add the default country code
        if ($defaultCountryCode && !str_starts_with($cleanPhone, '+')) {
            $normalizedPhone = $defaultCountryCode . ltrim($cleanPhone, '0');
            return $this->validateE164($normalizedPhone) ? $normalizedPhone : null;
        }
        
        return null;
    }

    /**
     * Normalize Jordanian phone number specifically
     */
    private function normalizeJordanianPhone(string $phone): ?string
    {
        // Remove leading zeros
        $phone = ltrim($phone, '0');
        
        // Jordanian mobile numbers start with 7
        if (str_starts_with($phone, '7') && strlen($phone) === 9) {
            return '+962' . $phone;
        }
        
        // Handle landline numbers (if needed)
        if (strlen($phone) === 8 && !str_starts_with($phone, '7')) {
            return '+962' . $phone;
        }
        
        return null;
    }

    /**
     * Validate if phone is in proper E.164 format
     */
    private function validateE164(string $phone): bool
    {
        // E.164 format: + followed by up to 15 digits
        return preg_match('/^\+[1-9]\d{1,14}$/', $phone) === 1;
    }

    /**
     * Extract country code from E.164 formatted number
     */
    public function extractCountryCode(string $e164Phone): string
    {
        // Try to match known country codes
        foreach ($this->countryCodes as $code => $country) {
            if (str_starts_with($e164Phone, $code)) {
                return $code;
            }
        }
        
        // Fallback: extract first 1-4 digits after +
        if (preg_match('/^(\+\d{1,4})/', $e164Phone, $matches)) {
            return $matches[1];
        }
        
        return '+962'; // Default to Jordan
    }

    /**
     * Format phone for display
     */
    public function formatForDisplay(string $e164Phone): string
    {
        $countryCode = $this->extractCountryCode($e164Phone);
        $localNumber = substr($e164Phone, strlen($countryCode));
        
        if ($countryCode === '+962' && strlen($localNumber) === 9) {
            // Format Jordanian numbers: +962 7X XXX XXXX
            return $countryCode . ' ' . substr($localNumber, 0, 2) . ' ' . 
                   substr($localNumber, 2, 3) . ' ' . substr($localNumber, 5);
        }
        
        // Generic formatting
        return $countryCode . ' ' . $localNumber;
    }

    /**
     * Check if two phone numbers are the same (ignoring format differences)
     */
    public function arePhonesSame(string $phone1, string $phone2, ?string $defaultCountryCode = '+962'): bool
    {
        $normalized1 = $this->normalizePhone($phone1, $defaultCountryCode);
        $normalized2 = $this->normalizePhone($phone2, $defaultCountryCode);
        
        return $normalized1 && $normalized2 && $normalized1 === $normalized2;
    }

    /**
     * Get available country codes
     */
    public function getCountryCodes(): array
    {
        return $this->countryCodes;
    }
}
<?php

namespace App\Services;

class PhoneService
{
    private array $countryCodes = [
        // Middle East & Arab Countries
        '+962' => 'Jordan',
        '+970' => 'Palestine',
        '+972' => 'Israel',
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
        '+98' => 'Iran',
        '+90' => 'Turkey',
        
        // Europe
        '+49' => 'Germany',
        '+33' => 'France',
        '+39' => 'Italy',
        '+34' => 'Spain',
        '+44' => 'United Kingdom',
        '+31' => 'Netherlands',
        '+32' => 'Belgium',
        '+41' => 'Switzerland',
        '+43' => 'Austria',
        '+45' => 'Denmark',
        '+46' => 'Sweden',
        '+47' => 'Norway',
        '+358' => 'Finland',
        '+48' => 'Poland',
        '+420' => 'Czech Republic',
        '+36' => 'Hungary',
        '+30' => 'Greece',
        '+351' => 'Portugal',
        '+353' => 'Ireland',
        '+7' => 'Russia',
        '+380' => 'Ukraine',
        '+40' => 'Romania',
        '+359' => 'Bulgaria',
        '+385' => 'Croatia',
        '+386' => 'Slovenia',
        '+421' => 'Slovakia',
        '+372' => 'Estonia',
        '+371' => 'Latvia',
        '+370' => 'Lithuania',
        '+355' => 'Albania',
        '+381' => 'Serbia',
        '+382' => 'Montenegro',
        '+387' => 'Bosnia and Herzegovina',
        '+389' => 'North Macedonia',
        '+377' => 'Monaco',
        '+378' => 'San Marino',
        '+39' => 'Vatican City',
        '+356' => 'Malta',
        '+354' => 'Iceland',
        '+423' => 'Liechtenstein',
        '+376' => 'Andorra',
        '+373' => 'Moldova',
        '+375' => 'Belarus',
        
        // North America
        '+1' => 'USA/Canada',
        '+52' => 'Mexico',
        '+53' => 'Cuba',
        '+1' => 'Jamaica',
        '+1' => 'Dominican Republic',
        '+509' => 'Haiti',
        '+502' => 'Guatemala',
        '+503' => 'El Salvador',
        '+504' => 'Honduras',
        '+505' => 'Nicaragua',
        '+506' => 'Costa Rica',
        '+507' => 'Panama',
        '+501' => 'Belize',
        
        // South America
        '+55' => 'Brazil',
        '+54' => 'Argentina',
        '+56' => 'Chile',
        '+51' => 'Peru',
        '+57' => 'Colombia',
        '+58' => 'Venezuela',
        '+593' => 'Ecuador',
        '+595' => 'Paraguay',
        '+598' => 'Uruguay',
        '+591' => 'Bolivia',
        '+592' => 'Guyana',
        '+597' => 'Suriname',
        '+594' => 'French Guiana',
        
        // Asia
        '+86' => 'China',
        '+81' => 'Japan',
        '+82' => 'South Korea',
        '+91' => 'India',
        '+92' => 'Pakistan',
        '+880' => 'Bangladesh',
        '+94' => 'Sri Lanka',
        '+960' => 'Maldives',
        '+977' => 'Nepal',
        '+975' => 'Bhutan',
        '+95' => 'Myanmar',
        '+66' => 'Thailand',
        '+84' => 'Vietnam',
        '+855' => 'Cambodia',
        '+856' => 'Laos',
        '+60' => 'Malaysia',
        '+65' => 'Singapore',
        '+62' => 'Indonesia',
        '+63' => 'Philippines',
        '+673' => 'Brunei',
        '+670' => 'East Timor',
        '+852' => 'Hong Kong',
        '+853' => 'Macau',
        '+886' => 'Taiwan',
        '+976' => 'Mongolia',
        '+996' => 'Kyrgyzstan',
        '+998' => 'Uzbekistan',
        '+992' => 'Tajikistan',
        '+993' => 'Turkmenistan',
        '+994' => 'Azerbaijan',
        '+995' => 'Georgia',
        '+374' => 'Armenia',
        '+7' => 'Kazakhstan',
        '+996' => 'Kyrgyzstan',
        '+93' => 'Afghanistan',
        
        // Africa
        '+212' => 'Morocco',
        '+213' => 'Algeria',
        '+216' => 'Tunisia',
        '+218' => 'Libya',
        '+249' => 'Sudan',
        '+211' => 'South Sudan',
        '+251' => 'Ethiopia',
        '+254' => 'Kenya',
        '+255' => 'Tanzania',
        '+256' => 'Uganda',
        '+250' => 'Rwanda',
        '+257' => 'Burundi',
        '+243' => 'Democratic Republic of Congo',
        '+242' => 'Republic of Congo',
        '+236' => 'Central African Republic',
        '+235' => 'Chad',
        '+237' => 'Cameroon',
        '+234' => 'Nigeria',
        '+233' => 'Ghana',
        '+229' => 'Benin',
        '+228' => 'Togo',
        '+225' => 'Ivory Coast',
        '+226' => 'Burkina Faso',
        '+223' => 'Mali',
        '+227' => 'Niger',
        '+221' => 'Senegal',
        '+220' => 'Gambia',
        '+224' => 'Guinea',
        '+245' => 'Guinea-Bissau',
        '+238' => 'Cape Verde',
        '+232' => 'Sierra Leone',
        '+231' => 'Liberia',
        '+27' => 'South Africa',
        '+267' => 'Botswana',
        '+268' => 'Eswatini',
        '+266' => 'Lesotho',
        '+264' => 'Namibia',
        '+260' => 'Zambia',
        '+263' => 'Zimbabwe',
        '+265' => 'Malawi',
        '+258' => 'Mozambique',
        '+261' => 'Madagascar',
        '+230' => 'Mauritius',
        '+248' => 'Seychelles',
        '+269' => 'Comoros',
        '+262' => 'Reunion',
        '+290' => 'Saint Helena',
        '+291' => 'Eritrea',
        '+252' => 'Somalia',
        '+253' => 'Djibouti',
        
        // Oceania
        '+61' => 'Australia',
        '+64' => 'New Zealand',
        '+679' => 'Fiji',
        '+685' => 'Samoa',
        '+676' => 'Tonga',
        '+677' => 'Solomon Islands',
        '+678' => 'Vanuatu',
        '+687' => 'New Caledonia',
        '+689' => 'French Polynesia',
        '+684' => 'American Samoa',
        '+683' => 'Niue',
        '+682' => 'Cook Islands',
        '+681' => 'Wallis and Futuna',
        '+680' => 'Palau',
        '+691' => 'Micronesia',
        '+692' => 'Marshall Islands',
        '+674' => 'Nauru',
        '+675' => 'Papua New Guinea',
        
        // Antarctica & Others
        '+672' => 'Antarctica',
        '+500' => 'Falkland Islands',
        '+590' => 'Guadeloupe',
        '+596' => 'Martinique',
        '+599' => 'Netherlands Antilles',
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
        } elseif ($defaultCountryCode === '+970') {
            return $this->normalizePalestinianPhone($cleanPhone);
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
     * Normalize Palestinian phone number specifically
     */
    private function normalizePalestinianPhone(string $phone): ?string
    {
        // Remove leading zeros
        $phone = ltrim($phone, '0');
        
        // Palestinian mobile numbers start with 59 (9 digits total)
        if (str_starts_with($phone, '59') && strlen($phone) === 9) {
            return '+970' . $phone;
        }
        
        // Handle landline numbers (8 digits, various area codes)
        if (strlen($phone) === 8 && !str_starts_with($phone, '59')) {
            return '+970' . $phone;
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
        } elseif ($countryCode === '+970' && strlen($localNumber) === 9) {
            // Format Palestinian numbers: +970 59 XXX XXXX
            return $countryCode . ' ' . substr($localNumber, 0, 2) . ' ' . 
                   substr($localNumber, 2, 3) . ' ' . substr($localNumber, 5);
        } elseif ($countryCode === '+49' && strlen($localNumber) >= 10) {
            // Format German numbers: +49 XXX XXXXXXX
            return $countryCode . ' ' . substr($localNumber, 0, 3) . ' ' . 
                   substr($localNumber, 3);
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

    /**
     * Get localized country codes
     */
    public function getLocalizedCountryCodes(): array
    {
        $localized = [];
        foreach ($this->countryCodes as $code => $country) {
            $localized[$code] = __('countries.' . $country, [], app()->getLocale()) ?: $country;
        }
        return $localized;
    }

    /**
     * Get country name by phone code
     */
    public function getCountryByCode(string $phoneCode): string
    {
        return $this->countryCodes[$phoneCode] ?? 'Unknown';
    }

    /**
     * Get localized country name by phone code
     */
    public function getLocalizedCountryByCode(string $phoneCode): string
    {
        $country = $this->getCountryByCode($phoneCode);
        return __('countries.' . $country, [], app()->getLocale()) ?: $country;
    }
}
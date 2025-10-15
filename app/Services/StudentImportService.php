<?php

namespace App\Services;

use App\Models\Student;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentImportService
{
    public function __construct(
        private PhoneService $phoneService,
        private StudentService $studentService,
    ) {}

    /**
     * Import students from an uploaded spreadsheet (XLSX/CSV)
     * Columns: [0]=full_name (Arabic), [1]=phone (Jordan), [2]=college, [3]=major
     */
    public function import(string $filePath): array
    {
        $created = 0; $duplicates = 0; $invalid = 0; $rows = 0;
        $errors = [];

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, false, false);

        foreach ($data as $i => $row) {
            $rows++;
            // Best-effort header detection: if phone column contains non-digit and non '+' chars, skip header
            if ($i === 0) {
                $firstPhone = trim((string)($row[1] ?? ''));
                if ($firstPhone && !preg_match('/^[+\d\s\-()]+$/u', $this->toWesternDigits($firstPhone))) {
                    continue; // header row
                }
            }

            $name = trim((string)($row[0] ?? ''));
            $rawPhone = trim((string)($row[1] ?? ''));
            $college = trim((string)($row[2] ?? ''));
            $major = trim((string)($row[3] ?? ''));

            // Convert Arabic-Indic digits to Western
            $rawPhone = $this->toWesternDigits($rawPhone);

            // Skip empty/zero phone
            if ($rawPhone === '' || $rawPhone === '0') {
                $invalid++;
                continue;
            }

            // Normalize phone for Jordan (+962)
            $normalized = $this->phoneService->normalizePhone($rawPhone, '+962');
            if (!$normalized) {
                $invalid++;
                continue;
            }

            // Skip duplicates
            if (Student::byPhone($normalized)->exists()) {
                $duplicates++;
                continue;
            }

            // Prepare data; default reach_source to Purchased Data for imports
            $fullName = $name !== '' ? $name : $normalized;
            $englishName = $this->transliterateArabicToEnglish($fullName);

            $payload = [
                'full_name' => $fullName,
                'full_name_en' => $englishName ?: null,
                'phone_primary' => $rawPhone,
                'country_code' => '+962',
                'reach_source' => 'Purchased Data',
                'college' => $college ?: null,
                'major' => $major ?: null,
            ];

            try {
                $this->studentService->createStudent($payload);
                $created++;
            } catch (\Throwable $e) {
                $invalid++;
                $errors[] = 'Row '.($i+1).': '.$e->getMessage();
            }
        }

        return compact('created', 'duplicates', 'invalid', 'rows', 'errors');
    }

    private function toWesternDigits(string $value): string
    {
        $easternArabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $persian =       ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $western =       ['0','1','2','3','4','5','6','7','8','9'];
        $value = str_replace($easternArabic, $western, $value);
        $value = str_replace($persian, $western, $value);
        return $value;
    }

    private function transliterateArabicToEnglish(string $value): ?string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }
        // Prefer intl transliterator if available
        if (class_exists('Transliterator')) {
            $trans = \Transliterator::create('Any-Latin; Latin-ASCII');
            if ($trans) {
                $out = $trans->transliterate($trimmed);
                $out = preg_replace('/[^A-Za-z\s\'-]/', ' ', $out) ?: '';
                $out = preg_replace('/\s+/', ' ', $out);
                $out = ucwords(strtolower(trim($out)));
                return $out !== '' ? $out : null;
            }
        }
        // Manual fallback mapping for Arabic letters
        $map = [
            'ا' => 'a','أ' => 'a','إ' => 'i','آ' => 'a','ب' => 'b','ت' => 't','ث' => 'th','ج' => 'j','ح' => 'h','خ' => 'kh',
            'د' => 'd','ذ' => 'dh','ر' => 'r','ز' => 'z','س' => 's','ش' => 'sh','ص' => 's','ض' => 'd','ط' => 't','ظ' => 'z',
            'ع' => 'a','غ' => 'gh','ف' => 'f','ق' => 'q','ك' => 'k','ل' => 'l','م' => 'm','ن' => 'n','ه' => 'h','و' => 'w',
            'ي' => 'y','ى' => 'a','ة' => 'a','ؤ' => 'w','ئ' => 'y','ﻻ' => 'la','لا' => 'la','ء' => ''
        ];
        $out = strtr($trimmed, $map);
        $out = preg_replace('/[^A-Za-z\s\'-]/', ' ', $out) ?: '';
        $out = preg_replace('/\s+/', ' ', $out);
        $out = ucwords(strtolower(trim($out)));
        return $out !== '' ? $out : null;
    }
}

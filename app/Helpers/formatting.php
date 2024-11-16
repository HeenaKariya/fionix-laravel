<?php

if (!function_exists('formatIndianCurrency')) {
    function formatIndianCurrency($number) {
        $number = number_format((float)$number, 2, '.', '');

        $exploded = explode('.', $number);
        $intPart = $exploded[0];
        $decPart = isset($exploded[1]) ? $exploded[1] : '00';

        $lastThreeDigits = substr($intPart, -3);
        $otherDigits = substr($intPart, 0, -3);

        if ($otherDigits != '') {
            $lastThreeDigits = ',' . $lastThreeDigits;
        }

        $formatted = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $otherDigits) . $lastThreeDigits;
        return $formatted . '.' . $decPart;
    }
}

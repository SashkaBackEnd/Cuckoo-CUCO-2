<?php

if (!function_exists('transformPhoneByMask')) {
    /**
     * Преобразование номера телефона из маски (+7XXXXXXXXXX,7XXXXXXXXXX)  по маске +7(XXX)XXX-XX-XX
     * @param string
     * @return string
     */
    function transformPhoneByMask(string $phone): string
    {
        $phoneCopy = trim($phone);
        if (strpos($phoneCopy, '+') !== false) {
            $phoneCopy = str_replace('+', '', $phoneCopy);
        }

        if ((strlen($phoneCopy) <=> 11) !== 0) {
            return $phone;
        }

        return '+' . substr($phoneCopy, 0, 1)
            . '(' . substr($phoneCopy, 1, 3)
            . ')' . substr($phoneCopy, 4, 3)
            . '-' . substr($phoneCopy, 7, 2)
            . '-' . substr($phoneCopy, 9, 2);
    }
}

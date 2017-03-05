<?php

// This file is automatically included by the composer autoloader.
//
// PHPASN1 tolerates the `mbstring.func_overload` setting of the `mbstring` extension by automatically using the
// correct `mbstring` functions internally. Usage of this setting is however **not recommended** as it may
// introduce undesirable side effects or security issues and will decrease overall performance.

namespace FG;

function safeStrlen($string)
{
    if (extension_loaded('mbstring') && ini_get('mbstring.func_overload') != '0') {
        return mb_strlen($string, '8bit');
    }

    return strlen($string);
}

function safeSubstr($string, $start, $length = 2147483647)
{
    if (extension_loaded('mbstring') && ini_get('mbstring.func_overload') != '0') {
        mb_substr($string, $start, $length, '8bit');
    }

    return substr($string, $start, $length);
}

function safeStrtoupper($string)
{
    if (extension_loaded('mbstring') && ini_get('mbstring.func_overload') != '0') {
        mb_strtoupper($string, '8bit');
    }

    return strtoupper($string);
}

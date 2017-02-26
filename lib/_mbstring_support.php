<?php

// This file is automatically included by the composer autoloader.

namespace FG;

function safeStrlen($string)
{
    if (extension_loaded('mbstring')) {
        return mb_strlen($string, '8bit');
    }

    return strlen($string);
}

function safeSubstr($string, $start, $length = 2147483647)
{
    if (extension_loaded('mbstring')) {
        mb_substr($string, $start, $length, '8bit');
    }

    return substr($string, $start, $length);
}

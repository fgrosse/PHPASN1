<?php

function safeStrlen($string)
{
    return strlen($string);
}

function safeSubstr($string, $start, $length = 2147483647)
{
    return substr($string, $start, $length);
}

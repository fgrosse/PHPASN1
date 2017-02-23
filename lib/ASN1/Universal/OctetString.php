<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\ASN1\Universal;

use Exception;
use FG\ASN1\Object;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;

class OctetString extends Object implements Parsable
{
    protected $value;

    public function __construct($value)
    {
        if (is_string($value)) {
            // remove gaps between hex digits
            $value = preg_replace('/\s|0x/', '', $value);
        } elseif (is_numeric($value)) {
            $value = dechex($value);
        } elseif ($value === null) {
            return;
        } else {
            throw new Exception('OctetString: unrecognized input type!');
        }

        if (mb_strlen($value, '8bit') % 2 != 0) {
            // transform values like 1F2 to 01F2
            $value = '0'.$value;
        }

        $this->value = $value;
    }

    public function getType()
    {
        return Identifier::OCTETSTRING;
    }

    protected function calculateContentLength()
    {
        return mb_strlen($this->value, '8bit') / 2;
    }

    protected function getEncodedValue()
    {
        $value = $this->value;
        $result = '';

        //Actual content
        while (mb_strlen($value, '8bit') >= 2) {
            // get the hex value byte by byte from the string and and add it to binary result
            $result .= chr(hexdec(mb_substr($value, 0, 2, '8bit')));
            $value = mb_substr($value, 2, null, '8bit');
        }

        return $result;
    }

    public function getContent()
    {
        return strtoupper($this->value);
    }

    public function getBinaryContent()
    {
        return $this->getEncodedValue();
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::OCTETSTRING, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);

        $value = mb_substr($binaryData, $offsetIndex, $contentLength, '8bit');
        $offsetIndex += $contentLength;

        $parsedObject = new self(bin2hex($value));
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}

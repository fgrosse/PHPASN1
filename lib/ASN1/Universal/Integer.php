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

class Integer extends Object implements Parsable
{
    /** @var int */
    private $value;

    /**
     * @param int $value
     * @throws Exception if the value is not numeric
     */
    public function __construct($value)
    {
        if (is_numeric($value) == false) {
            throw new Exception("Invalid VALUE [{$value}] for ASN1_INTEGER");
        }
        $this->value = $value;
    }

    public function getType()
    {
        return Identifier::INTEGER;
    }

    public function getContent()
    {
        return $this->value;
    }

    public function rightShift($number, $positions)
    {
        // Shift 1 right = div / 2
        return gmp_strval(gmp_div($number, gmp_pow(2, (int)$positions)));
    }

    protected function calculateContentLength()
    {
        $nrOfOctets = 1; // we need at least one octet
        $tmpValue = gmp_abs(gmp_init($this->value));
        while (gmp_cmp($tmpValue, 127) > 0) {
            $tmpValue = $this->rightShift($tmpValue, 8);
            $nrOfOctets++;
        }
        return $nrOfOctets;
    }

    protected function getEncodedValue()
    {
        $numericValue = $this->value;
        $contentLength = $this->getContentLength();

        if ($numericValue < 0) {
            $numericValue = gmp_add($numericValue, (gmp_sub(gmp_pow(2, 8 * $contentLength), 1)));
            $numericValue = gmp_add($numericValue, 1);
        }

        $result = '';
        for ($shiftLength = ($contentLength-1)*8; $shiftLength >= 0; $shiftLength -= 8) {
            $octet = gmp_strval(gmp_mod($this->rightShift($numericValue, $shiftLength), 256));
            $result .= chr($octet);
        }

        return $result;
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $parsedObject = new static(0);
        self::parseIdentifier($binaryData[$offsetIndex], $parsedObject->getType(), $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);

        $isNegative = (ord($binaryData[$offsetIndex]) & 0x80) != 0x00;

        $number = gmp_init(ord($binaryData[$offsetIndex++]) & 0x7F);

        for ($i = 0; $i<$contentLength-1; $i++) {
            $number = gmp_or(gmp_mul($number, 0x100), ord($binaryData[$offsetIndex++]));
        }

        if ($isNegative) {
            $number = gmp_sub($number, pow(2, 8*$contentLength-1));
        }

        $parsedObject->value = gmp_strval($number);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}

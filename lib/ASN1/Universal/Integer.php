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
use FG\Utility\BigInteger;
use FG\ASN1\ASNObject;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;

class Integer extends ASNObject implements Parsable
{
    /** @var int */
    private $value;

    /**
     * @param int $value
     *
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

    protected function calculateContentLength()
    {
        $nrOfOctets = 1; // we need at least one octet
        $tmpValue = BigInteger::create($this->value, 10);
        $tmpValue = $tmpValue->absoluteValue();
        while ($tmpValue->compare(127) > 0) {
            $tmpValue = $tmpValue->shiftRight(8);
            $nrOfOctets++;
        }
        return $nrOfOctets;
    }

    protected function getEncodedValue()
    {
        $numericValue = BigInteger::create($this->value);
        $contentLength = $this->getContentLength();

        if ($numericValue->isNegative()) {
            $numericValue = $numericValue->add(BigInteger::create(2)->toPower(8 * $contentLength)->subtract(1));
            $numericValue = $numericValue->add(1);
        }

        $result = '';
        for ($shiftLength = ($contentLength - 1) * 8; $shiftLength >= 0; $shiftLength -= 8) {
            $octet = $numericValue->shiftRight($shiftLength)->modulus(256)->toInteger();
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
        $number = BigInteger::create(ord($binaryData[$offsetIndex++]) & 0x7F);

        for ($i = 0; $i < $contentLength - 1; $i++) {
            $number = $number->multiply(0x100)->add(ord($binaryData[$offsetIndex++]));
        }

        if ($isNegative) {
            $number = $number->subtract(BigInteger::create(2)->toPower(8 * $contentLength - 1));
        }

        $parsedObject = new static((string)$number);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}

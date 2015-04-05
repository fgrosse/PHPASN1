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

use FG\ASN1\Parsable;
use FG\ASN1\Identifier;
use FG\ASN1\Exception\GeneralException;
use FG\ASN1\Exception\ParserException;

class RelativeObjectIdentifier extends ObjectIdentifier implements Parsable
{
    public function __construct($subIdentifiers)
    {
        $this->value = $subIdentifiers;
        $this->subIdentifiers = explode('.', $subIdentifiers);
        $nrOfSubIdentifiers = count($this->subIdentifiers);

        for ($i = 0; $i < $nrOfSubIdentifiers; $i++) {
            if (is_numeric($this->subIdentifiers[$i])) {
                // enforce the integer type
                $this->subIdentifiers[$i] = intval($this->subIdentifiers[$i]);
            } else {
                throw new GeneralException("[{$subIdentifiers}] is no valid object identifier (sub identifier ".($i+1)." is not numeric)!");
            }
        }
    }

    public function getType()
    {
        return Identifier::RELATIVE_OID;
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::RELATIVE_OID, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);

        $oidString = '';
        $octetsToRead = $contentLength;
        while ($octetsToRead > 0) {
            $number = 0;
            do {
                if ($octetsToRead == 0) {
                    throw new ParserException('Malformed ASN.1 Relative Object Identifier', $offsetIndex-1);
                }
                $octet = ord($binaryData[$offsetIndex++]);
                $number = ($number << 7) + ($octet & 0x7F);
                $octetsToRead--;
            } while ($octet & 0x80);
            $oidString .= "{$number}.";
        }

        // remove trailing '.'
        $oidString = substr($oidString, 0, -1);

        $parsedObject = new self($oidString);
        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}

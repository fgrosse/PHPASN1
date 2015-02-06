<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 *
 * Copyright © Friedrich Große, Berlin 2013
 *
 * PHPASN1 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPASN1 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPASN1.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace FG\X509\CSR;

use FG\ASN1\Object;
use FG\X509\CertificateExtensions;
use FG\ASN1\OID;
use FG\ASN1\Parsable;
use FG\ASN1\Construct;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\ObjectIdentifier;

class Attributes extends Construct implements Parsable
{
    public function getType()
    {
        return 0xA0;
    }

    public function addAttribute($objectIdentifier, Set $attribute)
    {
        if (is_string($objectIdentifier)) {
            $objectIdentifier = new ObjectIdentifier($objectIdentifier);
        }
        $attributeSequence = new Sequence($objectIdentifier, $attribute);
        $attributeSequence->getNumberOfLengthOctets();  // length and number of length octets is calculated
        $this->addChild($attributeSequence);
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], 0xA0, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $octetsToRead = $contentLength;

        $parsedObject = new Attributes();
        while ($octetsToRead > 0) {
            $initialOffset = $offsetIndex; // used to calculate how much bits have been read
            self::parseIdentifier($binaryData[$offsetIndex], Identifier::SEQUENCE, $offsetIndex++);
            self::parseContentLength($binaryData, $offsetIndex);

            $objectIdentifier = ObjectIdentifier::fromBinary($binaryData, $offsetIndex);
            $oidString = $objectIdentifier->getContent();
            if ($oidString == OID::PKCS9_EXTENSION_REQUEST) {
                $attribute = CertificateExtensions::fromBinary($binaryData, $offsetIndex);
            } else {
                $attribute = Object::fromBinary($binaryData, $offsetIndex);
            }

            $parsedObject->addAttribute($objectIdentifier, $attribute);
            $octetsToRead -= ($offsetIndex - $initialOffset);
        }

        $parsedObject->setContentLength($contentLength);

        return $parsedObject;
    }
}

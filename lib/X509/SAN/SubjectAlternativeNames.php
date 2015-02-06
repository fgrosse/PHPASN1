<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 *
 * Copyright © Friedrich Große, Berlin 2012
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

namespace FG\X509\SAN;

use FG\ASN1\Exception\ParserException;
use FG\ASN1\Object;
use FG\ASN1\OID;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\Sequence;

/**
 * See section 8.3.2.1 of ITU-T X.509
 */
class SubjectAlternativeNames extends Object implements Parsable
{
    private $alternativeNamesSequence;

    public function __construct()
    {
        $this->alternativeNamesSequence = new Sequence();
    }

    protected function calculateContentLength()
    {
        return $this->alternativeNamesSequence->getObjectLength();
    }

    public function getType()
    {
        return Identifier::OCTETSTRING;
    }

    public function addDomainName(DNSName $domainName)
    {
        $this->alternativeNamesSequence->addChild($domainName);
    }

    public function addIP(IPAddress $ip)
    {
        $this->alternativeNamesSequence->addChild($ip);
    }

    public function getContent()
    {
        return $this->alternativeNamesSequence->getContent();
    }

    protected function getEncodedValue()
    {
        return $this->alternativeNamesSequence->getBinary();
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::OCTETSTRING, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);

        if ($contentLength < 2) {
            throw new ParserException('Can not parse Subject Alternative Names: The Sequence within the octet string after the Object identifier '.OID::CERT_EXT_SUBJECT_ALT_NAME." is too short ({$contentLength} octets)", $offsetIndex);
        }

        $offsetOfSequence = $offsetIndex;
        $sequence = Sequence::fromBinary($binaryData, $offsetIndex);
        $offsetOfSequence += $sequence->getNumberOfLengthOctets() + 1;

        if ($sequence->getObjectLength() != $contentLength) {
            throw new ParserException("Can not parse Subject Alternative Names: The Sequence length does not match the length of the surrounding octet string", $offsetIndex);
        }

        $parsedObject = new SubjectAlternativeNames();
        /** @var Object $object */
        foreach ($sequence as $object) {
            if ($object->getType() == DNSName::IDENTIFIER) {
                $domainName = DNSName::fromBinary($binaryData, $offsetOfSequence);
                $parsedObject->addDomainName($domainName);
            } elseif ($object->getType() == IPAddress::IDENTIFIER) {
                $ip = IPAddress::fromBinary($binaryData, $offsetOfSequence);
                $parsedObject->addIP($ip);
            } else {
                throw new ParserException("Could not parse Subject Alternative Name: Only DNSName and IP SANs are currently supported", $offsetIndex);
            }
        }

        $parsedObject->getBinary(); // Determine the number of content octets and object sizes once (just to let the equality unit tests pass :/ )
        return $parsedObject;
    }
}

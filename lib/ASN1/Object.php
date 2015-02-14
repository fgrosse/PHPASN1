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

namespace FG\ASN1;

use FG\ASN1\Exception\ParserException;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Enumerated;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Null;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\RelativeObjectIdentifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\UTCTime;
use FG\ASN1\Universal\IA5String;
use FG\ASN1\Universal\PrintableString;
use FG\ASN1\Universal\NumericString;
use FG\ASN1\Universal\UTF8String;
use FG\ASN1\Universal\UniversalString;
use FG\ASN1\Universal\CharacterString;
use FG\ASN1\Universal\GeneralString;
use FG\ASN1\Universal\VisibleString;
use FG\ASN1\Universal\GraphicString;
use FG\ASN1\Universal\BMPString;
use FG\ASN1\Universal\T61String;
use FG\ASN1\Universal\ObjectDescriptor;

/**
 * Class Object is the base class for all concrete ASN.1 objects.
 */
abstract class Object implements Parsable
{
    private $contentLength;
    private $nrOfLengthOctets;

    /**
     * Must return the number of octets of the content part
     * @return int
     */
    abstract protected function calculateContentLength();

    /**
     * Encode the object using DER encoding
     * @see http://en.wikipedia.org/wiki/X.690#DER_encoding
     * @return string the binary representation of an objects value
     */
    abstract protected function getEncodedValue();

    /**
     * Return the content of this object in a non encoded form.
     * This can be used to print the value in human readable form
     * @return mixed
     */
    abstract public function getContent();

    /**
     * Return the object type octet.
     * This should use the class constants of Identifier
     * @see Identifier
     * @return int
     */
    abstract public function getType();

    /**
     * Encode this object using DER encoding
     * @return string the full binary representation of the complete object
     */
    public function getBinary()
    {
        $result  = chr($this->getType());
        $result .= $this->createLengthPart();
        $result .= $this->getEncodedValue();

        return $result;
    }

    private function createLengthPart()
    {
        $contentLength = $this->getContentLength();
        $nrOfLengthOctets = $this->getNumberOfLengthOctets($contentLength);

        if ($nrOfLengthOctets == 1) {
            return chr($contentLength);
        } else {
            // the first length octet determines the number subsequent length octets
            $lengthOctets = chr(0x80 | ($nrOfLengthOctets-1));
            for ($shiftLength = 8*($nrOfLengthOctets-2); $shiftLength >= 0; $shiftLength -= 8) {
                $lengthOctets .= chr($contentLength >> $shiftLength);
            }

            return $lengthOctets;
        }
    }

    protected function getNumberOfLengthOctets($contentLength = null)
    {
        if (!isset($this->nrOfLengthOctets)) {
            if ($contentLength == null) {
                $contentLength = $this->getContentLength();
            }

            $this->nrOfLengthOctets = 1;
            if ($contentLength > 127) {
                do { // long form
                    $this->nrOfLengthOctets++;
                    $contentLength = $contentLength >> 8;
                } while ($contentLength > 0);
            }
        }

        return $this->nrOfLengthOctets;
    }

    protected function getContentLength()
    {
        if (!isset($this->contentLength)) {
            $this->contentLength = $this->calculateContentLength();
        }

        return $this->contentLength;
    }

    protected function setContentLength($newContentLength)
    {
        $this->contentLength = $newContentLength;
        $this->getNumberOfLengthOctets($newContentLength);
    }

    /**
     * Returns the length of the whole object (including the identifier and length octets).
     */
    public function getObjectLength()
    {
        $nrOfIdentifierOctets = 1; // does not support identifier long form yet
        $contentLength = $this->getContentLength();
        $nrOfLengthOctets = $this->getNumberOfLengthOctets($contentLength);

        return $nrOfIdentifierOctets + $nrOfLengthOctets + $contentLength;
    }

    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * Returns the name of the ASN.1 Type of this object.
     *
     * @see Identifier::getName()
     */
    public function getTypeName()
    {
        return Identifier::getName($this->getType());
    }

    /**
     * @param string $binaryData
     * @param int $offsetIndex
     * @return \FG\ASN1\Object
     * @throws ParserException
     */
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        if (strlen($binaryData) < $offsetIndex) {
            throw new ParserException("Can not parse binary from data: Offset index larger than input size", $offsetIndex);
        }

        $identifierOctet = ord($binaryData[$offsetIndex]);
        if (Identifier::isContextSpecificClass($identifierOctet) && Identifier::isConstructed($identifierOctet)) {
            return ExplicitlyTaggedObject::fromBinary($binaryData, $offsetIndex);
        }

        switch ($identifierOctet) {
            case Identifier::BITSTRING:
                return BitString::fromBinary($binaryData, $offsetIndex);
            case Identifier::BOOLEAN:
                return Boolean::fromBinary($binaryData, $offsetIndex);
            case Identifier::ENUMERATED:
                return Enumerated::fromBinary($binaryData, $offsetIndex);
            case Identifier::INTEGER:
                return Integer::fromBinary($binaryData, $offsetIndex);
            case Identifier::NULL:
                return Null::fromBinary($binaryData, $offsetIndex);
            case Identifier::OBJECT_IDENTIFIER:
                return ObjectIdentifier::fromBinary($binaryData, $offsetIndex);
            case Identifier::RELATIVE_OID:
                return RelativeObjectIdentifier::fromBinary($binaryData, $offsetIndex);
            case Identifier::OCTETSTRING:
                return OctetString::fromBinary($binaryData, $offsetIndex);
            case Identifier::SEQUENCE:
                return Sequence::fromBinary($binaryData, $offsetIndex);
            case Identifier::SET:
                return Set::fromBinary($binaryData, $offsetIndex);
            case Identifier::UTC_TIME:
                return UTCTime::fromBinary($binaryData, $offsetIndex);
            case Identifier::IA5_STRING:
                return IA5String::fromBinary($binaryData, $offsetIndex);
            case Identifier::PRINTABLE_STRING:
                return PrintableString::fromBinary($binaryData, $offsetIndex);
            case Identifier::NUMERIC_STRING:
                return NumericString::fromBinary($binaryData, $offsetIndex);
            case Identifier::UTF8_STRING:
                return UTF8String::fromBinary($binaryData, $offsetIndex);
            case Identifier::UNIVERSAL_STRING:
                return UniversalString::fromBinary($binaryData, $offsetIndex);
            case Identifier::CHARACTER_STRING:
                return CharacterString::fromBinary($binaryData, $offsetIndex);
            case Identifier::GENERAL_STRING:
                return GeneralString::fromBinary($binaryData, $offsetIndex);
            case Identifier::VISIBLE_STRING:
                return VisibleString::fromBinary($binaryData, $offsetIndex);
            case Identifier::GRAPHIC_STRING:
                return GraphicString::fromBinary($binaryData, $offsetIndex);
            case Identifier::BMP_STRING:
                return BMPString::fromBinary($binaryData, $offsetIndex);
            case Identifier::T61_STRING:
                return T61String::fromBinary($binaryData, $offsetIndex);
            case Identifier::OBJECT_DESCRIPTOR:
                return ObjectDescriptor::fromBinary($binaryData, $offsetIndex);
            default:
                if (Identifier::isConstructed($identifierOctet)) {
                    return new UnknownConstructedObject($binaryData, $offsetIndex);
                } else {
                    $offsetIndex++;
                    $lengthOfUnknownObject = self::parseContentLength($binaryData, $offsetIndex);
                    $offsetIndex += $lengthOfUnknownObject;

                    return new UnknownObject($identifierOctet, $lengthOfUnknownObject);
                }
        }
    }

    protected static function parseIdentifier($identifierOctet, $expectedIdentifier, $offsetForExceptionHandling)
    {
        if (is_string($identifierOctet) || is_numeric($identifierOctet) == false) {
            $identifierOctet = ord($identifierOctet);
        }

        if ($identifierOctet != $expectedIdentifier) {
            $message = 'Can not create an '.Identifier::getName($expectedIdentifier).' from an '.Identifier::getName($identifierOctet);
            throw new ParserException($message, $offsetForExceptionHandling);
        }
    }

    protected static function parseContentLength(&$binaryData, &$offsetIndex, $minimumLength = 0)
    {
        $contentLength = ord($binaryData[$offsetIndex++]);

        if (($contentLength & 0x80) != 0) {
            // bit 8 is set -> this is the long form
            $nrOfLengthOctets = $contentLength & 0x7F;
            $contentLength = 0x00;
            for ($i = 0; $i < $nrOfLengthOctets; $i++) {
                $contentLength = ($contentLength << 8) + ord($binaryData[$offsetIndex++]);
            }
        }

        if ($contentLength < $minimumLength) {
            throw new ParserException('A '.get_called_class()." should have a content length of at least {$minimumLength}. Extracted length was {$contentLength}", $offsetIndex);
        }

        return $contentLength;
    }
}

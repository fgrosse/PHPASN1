<?php

namespace FG\X509;


use FG\ASN1\Exception\ParserException;
use FG\ASN1\Identifier;
use FG\ASN1\Object;
use FG\ASN1\OID;
use FG\ASN1\Parsable;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;

class ExtendedKeyUsage extends Object implements Parsable
{
    private $purposeSequence;

    public function __construct()
    {
        $this->purposeSequence = new Sequence();
    }

    public function getKeyPurposes()
    {
        return $this->purposeSequence->getChildren();
    }

    public function addKeyPurpose(ObjectIdentifier $oid)
    {
        $this->purposeSequence->addChild($oid);
    }

    public function getType()
    {
        return Identifier::SEQUENCE;
    }

    public function getContent()
    {
        return $this->purposeSequence->getContent();
    }

    protected function calculateContentLength()
    {
        return $this->purposeSequence->getObjectLength();
    }

    protected function getEncodedValue()
    {
        return $this->purposeSequence->getBinary();
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::SEQUENCE, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);

        if ($contentLength < 2) {
            throw new ParserException('Can not parse Subject Alternative Names: The Sequence within the octet string after the Object identifier '.OID::CERT_EXT_SUBJECT_ALT_NAME." is too short ({$contentLength} octets)", $offsetIndex);
        }

        $offsetOfSequence = $offsetIndex;
        $sequence = Sequence::fromBinary($binaryData, $offsetIndex);
        $offsetOfSequence += $sequence->getNumberOfLengthOctets() + 1;

        if ($sequence->getObjectLength() != $contentLength) {
            throw new ParserException('Can not parse Subject Alternative Names: The Sequence length does not match the length of the surrounding octet string', $offsetIndex);
        }

        $parsedObject = new self;
        /** @var Object $object */
        foreach ($sequence as $object) {
            if ($object->getType() == Identifier::OBJECT_IDENTIFIER) {
                $oid = ObjectIdentifier::fromBinary($binaryData, $offsetOfSequence);
                $parsedObject->addKeyPurpose($oid);
            } else {
                throw new ParserException('Could not parse Extended Key Usage: should only contain OID\'s', $offsetIndex);
            }
        }

        $parsedObject->getBinary(); // Determine the number of content octets and object sizes once (just to let the equality unit tests pass :/ )
        return $parsedObject;
    }
}
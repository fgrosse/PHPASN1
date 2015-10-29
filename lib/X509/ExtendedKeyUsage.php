<?php

namespace FG\X509;


use FG\ASN1\Exception\ParserException;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;

class ExtendedKeyUsage extends Sequence
{
    public function getKeyPurposes()
    {
        return $this->getChildren();
    }

    public function addKeyPurpose(ObjectIdentifier $oid)
    {
        $this->addChild($oid);
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $offsetOfSequence = $offsetIndex;
        $sequence = Sequence::fromBinary($binaryData, $offsetIndex);
        $offsetOfSequence += $sequence->getNumberOfLengthOctets() + 1;

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
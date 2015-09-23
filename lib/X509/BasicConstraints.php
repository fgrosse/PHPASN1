<?php

namespace FG\X509;


use FG\ASN1\Exception\ParserException;
use FG\ASN1\Identifier;
use FG\ASN1\Object;
use FG\ASN1\Parsable;
use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\Sequence;

class BasicConstraints extends Object implements Parsable
{
    /**
     * @var Sequence
     */
    private $constraintsSequence;

    public function __construct()
    {
        $isCa = new Boolean(false);
        $this->constraintsSequence = new Sequence();
        $this->constraintsSequence[0] = $isCa;
    }

    public function setCa(Boolean $boolean)
    {
        $this->constraintsSequence[0] = $boolean;
    }

    public function getCaValue()
    {
        return $this->constraintsSequence[0];
    }

    public function setPathLengthConstraint(Integer $integer)
    {
        $this->constraintsSequence[1] = $integer;
    }

    public function getPathLengthConstraint()
    {
        if (!isset($this->constraintsSequence[1])) {
            return null;
        }

        return $this->constraintsSequence->getContent();
    }

    public function getType()
    {
        return Identifier::SEQUENCE;
    }

    public function getContent()
    {
        return $this->constraintsSequence->getContent();
    }

    protected function calculateContentLength()
    {
        return $this->constraintsSequence->getObjectLength();
    }

    protected function getEncodedValue()
    {
        return $this->constraintsSequence->getBinary();
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::SEQUENCE, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);

        if ($contentLength < 1) {
            throw new ParserException('Can not parse Extended Key Usage', $offsetIndex);
        }

        $offsetOfSequence = $offsetIndex;
        $sequence = Sequence::fromBinary($binaryData, $offsetIndex);
        $offsetOfSequence += $sequence->getNumberOfLengthOctets() + 1;

        if ($sequence->getObjectLength() != $contentLength) {
            throw new ParserException('Can not parse Extended Key Usage: The Sequence length does not match the length of the surrounding octet string', $offsetIndex);
        }

        $parsedObject = new self();
        $isCa = Boolean::fromBinary($binaryData, $offsetOfSequence);
        $parsedObject->setCa($isCa);
        if ($contentLength == 2) {
            /** @var \FG\ASN1\Universal\Integer $pathLength */
            $pathLength = Integer::fromBinary($binaryData, $offsetOfSequence);
            $parsedObject->setPathLengthConstraint($pathLength);
        }

        $parsedObject->getBinary(); // Determine the number of content octets and object sizes once (just to let the equality unit tests pass :/ )
        return $parsedObject;
    }
}
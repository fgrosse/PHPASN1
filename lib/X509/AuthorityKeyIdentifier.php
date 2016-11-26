<?php

namespace FG\X509;


use FG\ASN1\Exception\ParserException;
use FG\ASN1\Identifier;
use FG\ASN1\Object;
use FG\ASN1\Parsable;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Sequence;

class AuthorityKeyIdentifier extends Object implements Parsable
{
    /**
     * @var Sequence
     */
    private $authoritySequence;

    public function __construct()
    {
        $this->authoritySequence = new Sequence();
    }

    public function setKeyIdentifier($identifier)
    {
        $this->authoritySequence[0] = new OctetString($identifier);
    }

    public function getKeyIdentifier()
    {
        if (!isset($this->authoritySequence[0])) {
            return null;
        }

        return $this->authoritySequence[0];
    }

    // Todo: handling of CertAuthorityIssuer and CertAuthoritySerialNo

    public function getType()
    {
        return Identifier::SEQUENCE;
    }

    public function getContent()
    {
        return $this->authoritySequence->getContent();
    }

    protected function calculateContentLength()
    {
        return $this->authoritySequence->getObjectLength();
    }

    protected function getEncodedValue()
    {
        return $this->authoritySequence->getBinary();
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::SEQUENCE, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);

        if ($contentLength < 1) {
            throw new ParserException('Can not parse AuthorityKeyIdentifier', $offsetIndex);
        }

        $offsetOfSequence = $offsetIndex;
        $sequence = Sequence::fromBinary($binaryData, $offsetIndex);
        $offsetOfSequence += $sequence->getNumberOfLengthOctets() + 1;

        if ($sequence->getObjectLength() != $contentLength) {
            throw new ParserException('Can not parse Extended Key Usage: The Sequence length does not match the length of the surrounding octet string', $offsetIndex);
        }

        $parsedObject = new self();
        if (isset($parsedObject[0])) {
            $octetString = OctetString::fromBinary($binaryData, $offsetOfSequence);
            $parsedObject->setKeyIdentifier($octetString->getContent());
        }

        if ($contentLength == 3) {
            $authorityIssuer = OctetString::fromBinary($binaryData, $offsetOfSequence);
            $authoritySerialNo = Integer::fromBinary($binaryData, $offsetOfSequence);
            // Todo: handling of CertAuthorityIssuer and CertAuthoritySerialNo
        }

        return $parsedObject;
    }
}
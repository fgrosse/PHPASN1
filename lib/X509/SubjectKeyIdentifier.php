<?php

namespace FG\X509;


use FG\ASN1\Universal\OctetString;

class SubjectKeyIdentifier extends OctetString
{
    public function __construct($identifier)
    {
        parent::__construct($identifier);
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $octetString = parent::fromBinary($binaryData, $offsetIndex);
        $subjectIdentifier = new self($octetString->getContent());
        return $subjectIdentifier;
    }
}
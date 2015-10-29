<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\X509;

use FG\ASN1\Exception\ParserException;
use FG\ASN1\OID;
use FG\ASN1\Object;
use FG\ASN1\Parsable;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\OctetString;
use FG\ASN1\Universal\Set;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\X509\SAN\SubjectAlternativeNames;

class CertificateExtensions extends Set implements Parsable
{
    private $innerSequence;
    private $extensions = [];

    public function __construct()
    {
        $this->innerSequence = new Sequence();
        parent::__construct($this->innerSequence);
    }

    public function addBasicConstraints(BasicConstraints $constraints)
    {
        $this->addExtension(OID::CERT_EXT_BASIC_CONSTRAINTS, $constraints);
    }

    public function addKeyUsage(KeyUsage $keyUsage)
    {
        $this->addExtension(OID::CERT_EXT_KEY_USAGE, $keyUsage);
    }

    public function addExtendedKeyUsage(ExtendedKeyUsage $usage)
    {
        $this->addExtension(OID::CERT_EXT_EXTENDED_KEY_USAGE, $usage);
    }

    public function addSubjectAlternativeNames(SubjectAlternativeNames $sans)
    {
        $this->addExtension(OID::CERT_EXT_SUBJECT_ALT_NAME, $sans);
    }

    public function addSubjectKeyIdentifier(SubjectKeyIdentifier $identifier)
    {
        $this->addExtension(
            OID::CERT_EXT_SUBJECT_KEY_IDENTIFIER,
            $identifier
        );
    }

    public function addAuthorityKeyIdentifier(AuthorityKeyIdentifier $identifier)
    {
        $this->addExtension(
            OID::CERT_EXT_AUTHORITY_KEY_IDENTIFIER,
            $identifier
        );
    }

    private function addEmbeddedExtension($oidString, Object $extension)
    {
        $internal = bin2hex($extension->getBinary());
        $sequence = new Sequence();
        $sequence->addChild(new ObjectIdentifier($oidString));
        $sequence->addChild(new OctetString($internal));

        $this->innerSequence->addChild($sequence);
        $this->extensions[] = $extension;
    }

    private function addExtension($oidString, Object $extension)
    {
        $sequence = new Sequence();
        $sequence->addChild(new ObjectIdentifier($oidString));
        $sequence->addChild($extension);

        $this->innerSequence->addChild($sequence);
        $this->extensions[] = $extension;
    }

    public function getContent()
    {
        return $this->extensions;
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::SET, $offsetIndex++);
        self::parseContentLength($binaryData, $offsetIndex);

        $tmpOffset = $offsetIndex;
        $extensions = Sequence::fromBinary($binaryData, $offsetIndex);
        $tmpOffset += 1 + $extensions->getNumberOfLengthOctets();

        $parsedObject = new self();
        foreach ($extensions as $extension) {
            if ($extension->getType() != Identifier::SEQUENCE) {
                //FIXME wrong offset index
                throw new ParserException('Could not parse Certificate Extensions: Expected ASN.1 Sequence but got '.$extension->getTypeName(), $offsetIndex);
            }

            $tmpOffset += 1 + $extension->getNumberOfLengthOctets();
            $children = $extension->getChildren();
            if (count($children) < 2) {
                throw new ParserException('Could not parse Certificate Extensions: Needs at least two child elements per extension sequence (object identifier and octet string)', $tmpOffset);
            }
            /** @var Object $objectIdentifier */
            $objectIdentifier = $children[0];

            /** @var OctetString $octetString */
            $octetString = $children[1];

            if ($objectIdentifier->getType() != Identifier::OBJECT_IDENTIFIER) {
                throw new ParserException('Could not parse Certificate Extensions: Expected ASN.1 Object Identifier but got '.$extension->getTypeName(), $tmpOffset);
            }

            $tmpOffset += $objectIdentifier->getObjectLength();

            if ($objectIdentifier->getContent() == OID::CERT_EXT_SUBJECT_ALT_NAME) {
                $sans = SubjectAlternativeNames::fromBinary($binaryData, $tmpOffset);
                $parsedObject->addSubjectAlternativeNames($sans);
            } elseif ($objectIdentifier->getContent() == OID::CERT_EXT_BASIC_CONSTRAINTS) {
                $constraints = BasicConstraints::fromBinary($binaryData, $tmpOffset);
                $parsedObject->addBasicConstraints($constraints);
            } elseif ($objectIdentifier->getContent() == OID::CERT_EXT_KEY_USAGE) {
                $keyUsage = KeyUsage::fromBinary($binaryData, $tmpOffset);
                $parsedObject->addKeyUsage($keyUsage);
            } elseif ($objectIdentifier->getContent() == OID::CERT_EXT_EXTENDED_KEY_USAGE) {
                $extKeyUsage = ExtendedKeyUsage::fromBinary($binaryData, $tmpOffset);
                $parsedObject->addExtendedKeyUsage($extKeyUsage);
            } elseif ($objectIdentifier->getContent() == OID::CERT_EXT_SUBJECT_KEY_IDENTIFIER) {
                $subjectKey = SubjectKeyIdentifier::fromBinary($binaryData, $tmpOffset);
                $parsedObject->addSubjectKeyIdentifier($subjectKey);
            } else {
                // can now only parse SANs. There might be more in the future
                $tmpOffset += $octetString->getObjectLength();
            }
        }

        $parsedObject->getBinary(); // Determine the number of content octets and object sizes once (just to let the equality unit tests pass :/ )
        return $parsedObject;
    }
}

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
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Boolean;
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

    public function addSubjectAlternativeNames(SubjectAlternativeNames $sans)
    {
        $this->addExtension(OID::CERT_EXT_SUBJECT_ALT_NAME, $sans);
    }

    public function addSubjectKeyIdentifier($hashHex)
    {
        $this->addEmbeddedExtension(
            OID::CERT_EXT_SUBJECT_KEY_IDENTIFIER,
            new OctetString($hashHex)
        );
    }

    public function addIssuerKeyIdentifier($hashHex)
    {
        $this->addEmbeddedExtension(
            OID::CERT_EXT_AUTHORITY_KEY_IDENTIFIER,
            new Sequence(new OctetString($hashHex))
        );
    }

    public function addCertAuthorityConstraint($flag)
    {
        $this->addEmbeddedExtension(
            OID::CERT_EXT_BASIC_CONSTRAINTS,
            new Sequence(new Boolean($flag))
        );
    }

    public function addKeyUsage($bitmap)
    {
        $this->addExtension(
            OID::CERT_EXT_KEY_USAGE,
            new BitString(dechex($bitmap))
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
            } elseif ($objectIdentifier->getContent() == OID::CERT_EXT_SUBJECT_KEY_IDENTIFIER) {
                $identifier = Object::fromBinary(hex2bin($octetString->getContent()))->getContent();
                $parsedObject->addSubjectKeyIdentifier($identifier);
            } elseif ($objectIdentifier->getContent() == OID::CERT_EXT_AUTHORITY_KEY_IDENTIFIER) {
                $identifier = Object::fromBinary(hex2bin($octetString->getContent()))[0]->getContent();
                $parsedObject->addIssuerKeyIdentifier($identifier);
            } elseif ($objectIdentifier->getContent() == OID::CERT_EXT_BASIC_CONSTRAINTS) {
                $sequence = Object::fromBinary(hex2bin($octetString->getContent()));
                $bool = $sequence->getContent()[0]->getContent() == 'TRUE';
                $parsedObject->addCertAuthorityConstraint($bool);
            } elseif ($objectIdentifier->getContent() == OID::CERT_EXT_KEY_USAGE) {
                $dec = (int) gmp_strval(gmp_init($octetString->getContent(), 16), 10);
                $parsedObject->addKeyUsage($dec);
            } else {
                // can now only parse SANs. There might be more in the future
                $tmpOffset += $octetString->getObjectLength();
            }
        }

        $parsedObject->getBinary(); // Determine the number of content octets and object sizes once (just to let the equality unit tests pass :/ )
        return $parsedObject;
    }
}

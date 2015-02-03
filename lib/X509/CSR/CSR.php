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

namespace FG\X509\CSR;

use FG\ASN1\OID;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Sequence;
use FG\X509\CertificateSubject;
use FG\X509\AlgorithmIdentifier;
use FG\X509\PublicKey;

class CSR extends Sequence
{
    const CSR_VERSION_NR = 0;

    protected $subject;
    protected $publicKey;
    protected $signature;
    protected $signatureAlgorithm;

    protected $startSequence;

    /**
     * @param string $commonName
     * @param string $email
     * @param string $organization
     * @param string $locality
     * @param string $state
     * @param string $country
     * @param string $organizationalUnit
     * @param string $publicKey
     * @param string $signature
     * @param string $signatureAlgorithm
     */
    public function __construct($commonName, $email, $organization, $locality, $state, $country, $organizationalUnit, $publicKey, $signature, $signatureAlgorithm = OID::SHA1_WITH_RSA_SIGNATURE)
    {
        $this->subject = new CertificateSubject(
            $commonName,
            $email,
            $organization,
            $locality,
            $state,
            $country,
            $organizationalUnit
        );
        $this->publicKey = $publicKey;
        $this->signature = $signature;
        $this->signatureAlgorithm = $signatureAlgorithm;

        $this->createCSRSequence();
    }

    protected function createCSRSequence()
    {
        $versionNr            = new Integer(self::CSR_VERSION_NR);
        $publicKey            = new PublicKey($this->publicKey);
        $signature            = new BitString($this->signature);
        $signatureAlgorithm    = new AlgorithmIdentifier($this->signatureAlgorithm);

        $certRequestInfo  = new Sequence($versionNr, $this->subject, $publicKey);

        $this->addChild($certRequestInfo);
        $this->addChild($signatureAlgorithm);
        $this->addChild($signature);
    }

    public function __toString()
    {
        $tmp = base64_encode($this->getBinary());

        for ($i = 0; $i < strlen($tmp); $i++) {
            if (($i+2) % 65 == 0) {
                $tmp = substr($tmp, 0, $i+1)."\n".substr($tmp, $i+1);
            }
        }

        $result = "-----BEGIN CERTIFICATE REQUEST-----".PHP_EOL;
        $result .= $tmp.PHP_EOL;
        $result .= "-----END CERTIFICATE REQUEST-----";

        return $result;
    }

    public function getVersion()
    {
        return self::CSR_VERSION_NR;
    }
    public function getOrganizationName()
    {
        return $this->subject->getOrganization();
    }
    public function getLocalName()
    {
        return $this->subject->getLocality();
    }
    public function getState()
    {
        return $this->subject->getState();
    }
    public function getCountry()
    {
        return $this->subject->getCountry();
    }
    public function getOrganizationalUnit()
    {
        return $this->subject->getOrganizationalUnit();
    }
    public function getPublicKey()
    {
        return $this->publicKey;
    }
    public function getSignature()
    {
        return $this->signature;
    }
    public function getSignatureAlgorithm()
    {
        return $this->signatureAlgorithm;
    }
}

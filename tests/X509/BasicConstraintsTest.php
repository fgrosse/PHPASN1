<?php

namespace FG\Test\X509;


use FG\ASN1\Universal\Boolean;
use FG\ASN1\Universal\Integer;
use FG\Test\ASN1TestCase;
use FG\X509\BasicConstraints;

class BasicConstraintsTest extends ASN1TestCase
{
    public function testDefault()
    {
        $boolFalse = new Boolean(false);
        $constraint = new BasicConstraints();
        $this->assertEquals($boolFalse, $constraint->getCaValue());
        $this->assertEquals(null, $constraint->getPathLengthConstraint());
    }

    public function testSetValues()
    {
        $true = new Boolean(true);
        $length = new Integer(101);
        $constraint = new BasicConstraints();
        $constraint->setCa($true);
        $constraint->setPathLengthConstraint($length);
        $this->assertEquals($true, $constraint->getCaValue());
        $this->assertEquals($length, $constraint->getPathLengthConstraint());


    }

    public function testFromBinary()
    {
        $true = new Boolean(true);
        $length = new Integer(101);
        $constraint = new BasicConstraints();
        $constraint->setCa($true);
        $constraint->setPathLengthConstraint($length);
        $binary = $constraint->getBinary();

        $parsed = BasicConstraints::fromBinary($binary);
        $this->assertEquals($constraint, $parsed);
    }
}
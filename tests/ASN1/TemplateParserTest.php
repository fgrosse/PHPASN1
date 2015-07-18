<?php

namespace FG\Test\ASN1;

use FG\ASN1\Identifier;
use FG\ASN1\TemplateParser;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Integer;
use FG\ASN1\Universal\ObjectIdentifier;
use FG\ASN1\Universal\Sequence;
use FG\ASN1\Universal\Set;
use PHPUnit_Framework_TestCase;

class TemplateParserTest extends PHPUnit_Framework_TestCase
{
    public function testParseBase64()
    {
        $sequence = new Sequence(
            new Set(
                new ObjectIdentifier('1.2.250.1.16.9'),
                new Sequence(
                    new Integer(42),
                    new BitString('A0 12 00 43')
                )
            )
        );

        $data = base64_encode($sequence->getBinary());

        $template = array(
            Identifier::SEQUENCE => array(
                Identifier::SET => array(
                    Identifier::OBJECT_IDENTIFIER,
                    Identifier::SEQUENCE => array(
                        Identifier::INTEGER,
                        Identifier::BITSTRING,
                    )
                )
            )
        );

        $parser = new TemplateParser();
        $object = $parser->parseBase64($data, $template);
        $this->assertInstanceOf('FG\ASN1\Universal\Set', $object[0]);
        $this->assertInstanceOf('FG\ASN1\Universal\ObjectIdentifier', $object[0][0]);
        $this->assertInstanceOf('FG\ASN1\Universal\Sequence', $object[0][1]);
        $this->assertInstanceOf('FG\ASN1\Universal\Integer', $object[0][1][0]);
        $this->assertInstanceOf('FG\ASN1\Universal\BitString', $object[0][1][1]);
    }
}

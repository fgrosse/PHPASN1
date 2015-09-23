<?php

namespace FG\X509;


use FG\ASN1\Identifier;
use FG\ASN1\Object;
use FG\ASN1\Parsable;
use FG\ASN1\Universal\BitString;

class KeyUsage extends Object implements Parsable
{
    /**
     * @var resource|\Gmp
     */
    private $mask;
    /**
     * @var bool
     */
    private $changed = false;

    /**
     * @var BitString
     */
    private $bitString;

    public function __construct()
    {
        $this->mask = gmp_init(0, 10);
        $this->bitString = new BitString(0);
    }

    public function addBits($flags)
    {
        $theirBits = gmp_init($flags, 10);

        for ($i = 0; $i < 8; $i++) {
            if (gmp_testbit($theirBits, $i) && !$this->testBit($i)) {
                $this->setBit($i);
            }
        }
    }

    public function setBit($index)
    {
        if (!($index >= 0 && $index < 8)) {
            throw new \InvalidArgumentException('KeyUsage: bit index must be within 0 - 7');
        }

        $this->changed = true;
        gmp_setbit($this->mask, $index);
    }

    public function testBit($index)
    {
        return gmp_testbit($this->mask, $index);
    }

    public function getType()
    {
        return Identifier::BITSTRING;
    }

    /**
     * @return BitString
     */
    public function getContent()
    {
        if ($this->changed) {
            $this->bitString = new BitString(gmp_strval($this->mask, 10));
        }

        return $this->bitString;
    }

    protected function calculateContentLength()
    {
        return $this->getContent()->calculateContentLength();
    }

    protected function getEncodedValue()
    {
        return $this->getContent()->getEncodedValue();
    }

    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $o = BitString::fromBinary($binaryData, $offsetIndex);
        $content = $o->getContent();
        if (strlen($content) !== 2) {
            throw new \RuntimeException('KeyUsage must be a Bit String of one byte');
        }

        $dec = hexdec($content);
        $parsedObject = new self;
        $parsedObject->addBits($dec);

        return $parsedObject;
    }
}
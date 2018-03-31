<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FG\Test\Utility;

use FG\Utility\BigInteger;
use PHPUnit\Framework\TestCase;

abstract class BigIntegerTest extends TestCase
{
    /**
     * Whether the current testing instance supports this implementation.
     * @return bool
     */
    abstract protected function _isSupported();

    /**
     * Return mode expected by BigInteger::setPrefer.
     * @return string
     */
    abstract protected function _getMode();

    protected function setUp()
    {
        if (!$this->_isSupported()) {
            $this->markTestSkipped(sprintf('Mode %s is not supported.', $this->_getMode()));
            return;
        }

        BigInteger::setPrefer($this->_getMode());
    }



    public function testCreateFromString()
    {
        $a = BigInteger::create('8888');
        $this->assertSame('8888', (string)$a);

        $a = BigInteger::create('0');
        $this->assertSame('0', (string)$a);

        $a = BigInteger::create('-8888');
        $this->assertSame('-8888', (string)$a);

        $a = BigInteger::create('18446744073709551616');
        $this->assertSame('18446744073709551616', (string)$a);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromInvalidString()
    {
        BigInteger::create('0x1');
    }

    public function testCreateFromInteger()
    {
        $a = BigInteger::create(8888);
        $this->assertSame('8888', (string)$a);

        $a = BigInteger::create(0);
        $this->assertSame('0', (string)$a);

        $a = BigInteger::create(-8888);
        $this->assertSame('-8888', (string)$a);

        $a = BigInteger::create(PHP_INT_MAX);
        $this->assertSame((string)PHP_INT_MAX, (string)$a);

        $a = BigInteger::create(PHP_INT_MIN);
        $this->assertSame((string)PHP_INT_MIN, (string)$a);
    }

    public function testToInteger()
    {
        $a = BigInteger::create(8888);
        $this->assertSame(8888, $a->toInteger());

        $a = BigInteger::create(0);
        $this->assertSame(0, $a->toInteger());

        $a = BigInteger::create(-8888);
        $this->assertSame(-8888, $a->toInteger());

        $a = BigInteger::create(PHP_INT_MAX);
        $this->assertSame(PHP_INT_MAX, $a->toInteger());
    }

    /**
     * @expectedException \OverflowException
     */
    public function testIntegerOverflow()
    {
        $a = BigInteger::create(PHP_INT_MAX);
        $a->add(1)->toInteger();
    }

    public function testClone()
    {
        $a = BigInteger::create('18446744073709551616');
        $b = clone $a;

        $this->assertEquals($a, $b);
        $this->assertSame('18446744073709551616', (string)$a);
        $this->assertSame('18446744073709551616', (string)$b);
    }

    public function testIsNegative()
    {
        $a = BigInteger::create('1');
        $this->assertFalse($a->isNegative());

        $a = BigInteger::create('0');
        $this->assertFalse($a->isNegative());

        $a = BigInteger::create('-0');
        $this->assertFalse($a->isNegative());

        $a = BigInteger::create('-1');
        $this->assertTrue($a->isNegative());
    }

    public function testCompare()
    {
        $a = BigInteger::create('-18446744073709551616');
        $b = BigInteger::create('18446744073709551616');
        $c = BigInteger::create('18446744073709551616');
        $d = BigInteger::create('18446744073709551617');

        $this->assertTrue($a->compare($b) < 0);
        $this->assertTrue($b->compare($a) > 0);

        $this->assertSame(0, $b->compare($c));
        $this->assertSame(0, $c->compare($b));

        $this->assertTrue($d->compare($c) > 0);
        $this->assertTrue($c->compare($d) < 0);
    }

    public function testAdd()
    {
        $a = BigInteger::create('1234');
        $b = BigInteger::create('5678');

        // test in both directions, also ensures immutability of original
        $c = $a->add($b);
        $d = $b->add($a);

        // test equity
        $this->assertEquals($c, $d);

        // test result
        $this->assertSame('6912', (string)$c);
        $this->assertSame('6912', (string)$d);

        // with one negative number
        $b = BigInteger::create('-5678');

        // test in both directions, also ensures immutability of original
        $c = $a->add($b);
        $d = $b->add($a);

        // test equity
        $this->assertEquals($c, $d);

        // test result
        $this->assertSame('-4444', (string)$c);
        $this->assertSame('-4444', (string)$d);

        // with two negative numbers
        $a = BigInteger::create('-1234');

        // test in both directions, also ensures immutability of original
        $c = $a->add($b);
        $d = $b->add($a);

        // test equity
        $this->assertEquals($c, $d);

        // test result
        $this->assertSame('-6912', (string)$c);
        $this->assertSame('-6912', (string)$d);

        // large number
        $a = BigInteger::create('18446744073709551615');
        $this->assertSame('18446744073709551616', (string)$a->add(1));
    }

    public function testSubtract()
    {
        $a = BigInteger::create('1234');
        $b = BigInteger::create('5678');

        // test in both directions, also ensures immutability of original
        $this->assertSame('-4444', (string)$a->subtract($b));
        $this->assertSame('4444', (string)$b->subtract($a));

        // with one negative number
        $b = BigInteger::create('-5678');

        // test in both directions, also ensures immutability of original
        $this->assertSame('6912', (string)$a->subtract($b));
        $this->assertSame('-6912', (string)$b->subtract($a));

        // with two negative numbers
        $a = BigInteger::create('-1234');

        // test in both directions, also ensures immutability of original
        $this->assertSame('4444', (string)$a->subtract($b));
        $this->assertSame('-4444', (string)$b->subtract($a));

        // large number
        $a = BigInteger::create('18446744073709551616');
        $this->assertSame('18446744073709551615', (string)$a->subtract(1));
    }

    public function testMultiply()
    {
        $a = BigInteger::create('4294967296'); // 2^32
        $b = BigInteger::create('18446744073709551616'); // 2^64

        $c = $a->multiply($b);
        $d = $b->multiply($a);

        // equity
        $this->assertEquals($c, $d);

        // 2^96
        $this->assertSame('79228162514264337593543950336', (string)$c);
        $this->assertSame('79228162514264337593543950336', (string)$d);
    }

    public function testModulus()
    {
        $a = BigInteger::create('18446744073709551617'); // 2^64 + 1
        $b = BigInteger::create('4294967296'); // 2^32
        $this->assertSame('1', (string)$a->modulus($b));{

    }
        $a = BigInteger::create('-18446744073709551615'); // 2^64 + 1
        $b = BigInteger::create('4294967296'); // 2^32
        $this->assertSame('1', (string)$a->modulus($b));
    }

    public function testToPower()
    {
        // 2^96
        $a = BigInteger::create(2);
        $this->assertSame('79228162514264337593543950336', (string)$a->toPower(96));

        // (2^64)^2 = 2^128
        $a = BigInteger::create('18446744073709551616');
        $this->assertSame('340282366920938463463374607431768211456', (string)$a->toPower(2));
    }

    public function testShiftRight()
    {
        // 2^64
        $a = BigInteger::create('18446744073709551616');
        $this->assertSame('4294967296', (string)$a->shiftRight(32));
        $this->assertSame('1', (string)$a->shiftRight(64));
    }

    public function testShiftLeft()
    {
        $a = BigInteger::create('1');
        $this->assertSame('4294967296', (string)$a->shiftLeft(32));
        $this->assertSame('18446744073709551616', (string)$a->shiftLeft(64));

    }

    public function testAbsoluteValue()
    {
        $a = BigInteger::create('18446744073709551616');
        $this->assertSame('18446744073709551616', (string)$a->absoluteValue());

        $a = BigInteger::create('-18446744073709551616');
        $this->assertSame('18446744073709551616', (string)$a->absoluteValue());
    }

    protected function tearDown()
    {
        BigInteger::setPrefer(null);
    }
}

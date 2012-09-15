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
 
namespace PHPASN1;

require_once('PHPASN1TestCase.class.php');
 
class ASN1ParserTest extends PHPASN1TestCase {
    
    private $parser;
    
    protected function setUp() {
        $this->parser = new ASN1Parser();
    }
    
    public function testExtractObjectLength() {
        $this->parser = new ASN1Parser();
                
        $binaryData = chr(0x03);        
        $length = $this->callMethod($this->parser, 'extractObjectLength', $binaryData, 0);
        $this->assertEquals(3, $length);
        
        $binaryData = chr(127);        
        $length = $this->callMethod($this->parser, 'extractObjectLength', $binaryData, 0);
        $this->assertEquals(127, $length);
        
        $binaryData = chr(0x80 + 1);        
        $binaryData .= chr(128);
        $length = $this->callMethod($this->parser, 'extractObjectLength', $binaryData, 0);
        $this->assertEquals(128, $length);
        
        $binaryData = chr(0x80 + 1);        
        $binaryData .= chr(255);
        $length = $this->callMethod($this->parser, 'extractObjectLength', $binaryData, 0);
        $this->assertEquals(255, $length);
        
        $binaryData = chr(0x80 + 2);        
        $binaryData .= chr(1);
        $binaryData .= chr(0);
        $length = $this->callMethod($this->parser, 'extractObjectLength', $binaryData, 0);
        $this->assertEquals(256, $length);
        
        $binaryData = chr(0x80 + 2);        
        $binaryData .= chr(1);
        $binaryData .= chr(255);
        $length = $this->callMethod($this->parser, 'extractObjectLength', $binaryData, 0);
        $this->assertEquals(511, $length);
        
        // Test if this does also work with the offset parameter
        $garbage = 'This is some random garbage at the beginning';
        $binaryData = $garbage;
        $binaryData .= chr(0x80 + 2);        
        $binaryData .= chr(1);
        $binaryData .= chr(255);
        $length = $this->callMethod($this->parser, 'extractObjectLength', $binaryData, strlen($garbage));
        $this->assertEquals(511, $length);              
    }
 
    public function testParseASNBitString() {
        $binaryData  = chr(ASN_Object::ASN1_BITSTRING);
        $binaryData .= chr(0x05); // length
        $binaryData .= chr(0);    // number of unused bits        
        $binaryData .= chr(0xA0); // bit string...
        $binaryData .= chr(0x3F);
        $binaryData .= chr(0x00);
        $binaryData .= chr(0x45);
        $object = $this->parser->parse($binaryData);
        $this->assertEquals(new ASN_BitString('a03f0045'), $object);
    }
    
    public function testParseASNBoolean() {
        $binaryData  = chr(ASN_Object::ASN1_BOOLEAN);
        $binaryData .= chr(0x01); // length
        
        $binaryContent = chr(0x00);         
        $object = $this->parser->parse($binaryData.$binaryContent);
        $this->assertEquals(new ASN_Boolean(false), $object);
        
        $binaryContent = chr(0x3F);
        $object = $this->parser->parse($binaryData.$binaryContent);
        $this->assertEquals(new ASN_Boolean(false), $object);
        
        $binaryContent = chr(0xFF);
        $object = $this->parser->parse($binaryData.$binaryContent);
        $this->assertEquals(new ASN_Boolean(true), $object);                
    }

    /**
     * @expectedException PHPASN1\ASN1ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Boolean should not have a length other than one. Extracted length was 2
     */
    public function testParseASNBooleanWithInvalidLength01() {
        $binaryData  = chr(ASN_Object::ASN1_BOOLEAN);
        $binaryData .= chr(0x02);
        $binaryData .= chr(0xFF);
        $this->parser->parse($binaryData);        
    }
    
    /**
     * @expectedException PHPASN1\ASN1ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Boolean should not have a length other than one. Extracted length was 0
     */
    public function testParseASNBooleanWithInvalidLength02() {
        $binaryData  = chr(ASN_Object::ASN1_BOOLEAN);
        $binaryData .= chr(0x00);
        $binaryData .= chr(0xFF);
        $this->parser->parse($binaryData);        
    }
    
    public function testParseASNInteger() {
        $type = chr(ASN_Object::ASN1_INTEGER);
        $length = chr(0x01);
        
        $value = chr(0);
        $object = $this->parser->parse($type.$length.$value);
        $this->assertEquals(new ASN_Integer(0), $object);                            
        
        $value = chr(123);
        $object = $this->parser->parse($type.$length.$value);
        $this->assertEquals(new ASN_Integer(123), $object);
        
        $value = chr(128);
        $object = $this->parser->parse($type.$length.$value);
        $this->assertEquals(new ASN_Integer(-128), $object);
        
        $value = chr(255);
        $object = $this->parser->parse($type.$length.$value);
        $this->assertEquals(new ASN_Integer(-1), $object);
        
        // 2 Byte integers
        $length = chr(0x02);
        $value = chr(5);
        $value .= chr(133);
        $object = $this->parser->parse($type.$length.$value);
        $this->assertEquals(new ASN_Integer(1413), $object);
                
        $value = chr(133);
        $value .= chr(133);
        $object = $this->parser->parse($type.$length.$value);
        $this->assertEquals(new ASN_Integer(-31355), $object);                
    }

    public function testParseASNNull() {
        $type = chr(ASN_Object::ASN1_NULL);
        $length = chr(0x00);        
        $object = $this->parser->parse($type.$length);
        $this->assertEquals(new ASN_NULL(), $object);                                                   
    }
    
    /**
     * @expectedException PHPASN1\ASN1ParserException
     * @expectedExceptionMessage ASN.1 Parser Exception at offset 2: An ASN.1 Null should not have a length other than zero. Extracted length was 1
     */
    public function testParseASNNullWithInvalidLength() {
        $type = chr(ASN_Object::ASN1_NULL);
        $length = chr(0x01);
        $this->parser->parse($type.$length);        
    }
    
    public function testParseASNEnumerated() {
        $type = chr(ASN_Object::ASN1_ENUMERATED);
        $length = chr(0x01);        
        $value = chr(0x01);
        $object = $this->parser->parse($type.$length.$value);
        $this->assertEquals(new ASN_Enumerated(1), $object);                                                   
    }
}
?>
    
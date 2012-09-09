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

class CSR_SimpleObject extends ASN_Object{
	
	public function __construct($objectIdentifierString, ASN_Object $value) {						
		$this->value = new ASN_Set(
			new ASN_Sequence(new ASN_ObjectIdentifier($objectIdentifierString), $value)
        );
	}
	
    public function getType() {
        return self::ASN1_SET;
    }
    
	protected function getContentLength(){
		return $this->value->getContentLength();
	}
	
	public function getBinary() {
		return $this->value->getBinary();
	}
	
	protected function getEncodedValue(){
		return $this->value->getEncodedValue();
	}
		
}
?>
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

class ASN_Sequence extends ASN_Object {
	
	public function __construct(ASN_Object $child1 = null, ASN_Object $child2 = null, ASN_Object $childN = null) {
		$this->value = array();
		
        $children = func_get_args();
        
        foreach ($children as $child) {
            $this->addChild($child);
        }		
	}	
    
    public function getType() {
        return Identifier::SEQUENCE;
    }
    
    protected function calculateContentLength() {
        $length = 0;
        foreach($this->value as $component) {
            $length += $component->getObjectLength();
        }             
        return $length;
    }
    
	protected function getEncodedValue() {		
		$result = '';
		foreach($this->value as $component) {
			$result .= $component->getBinary();
		}
		return $result;
	}	
	
	public function addChild(ASN_Object $child) {
		$this->value[] = $child;
	}
	
	public function __toString() {
		return '['.get_called_class().']';       
	}
    
}
?>
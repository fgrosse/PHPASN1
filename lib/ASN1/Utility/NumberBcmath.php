<?php

namespace FG\ASN1\Utility;

/**
 * Class NumberBcmath
 * Integer representation of big numbers using the bcmath library to perform large operations.
 * @package FG\ASN1\Utility
 * @internal
 */
class NumberBcmath extends Number
{
	protected $_str;

	public function __clone() {
		// nothing needed to copy
	}

	protected function _fromString($str) {
		$this->_str = (string)$str;
	}

	public function __toString() {
		return $this->_str;
	}

	public function toInteger() {
		return (int)$this->_str;
	}

	public function isNegative() {
		return bccomp($this->_str, '0', 0) < 0;
	}

	protected function _unwrap($number) {
		if ($number instanceof self) {
			return $number->_str;
		}
		return $number;
	}

	public function compare($number) {
		return bccomp($this->_str, $this->_unwrap($number), 0);
	}

	public function add($b) {
		$ret = new self();
		$ret->_str = bcadd($this->_str, $this->_unwrap($b), 0);
		return $ret;
	}

	public function subtract($b) {
		$ret = new self();
		$ret->_str = bcsub($this->_str, $this->_unwrap($b), 0);
		return $ret;
	}

	public function multiply($b) {
		$ret = new self();
		$ret->_str = bcmul($this->_str, $this->_unwrap($b), 0);
		return $ret;
	}

	public function modulus($b) {
		$ret = new self();
		$ret->_str = bcmod($this->_str, $this->_unwrap($b));
		return $ret;
	}

	public function toPower($b) {
		$ret = new self();
		$ret->_str = bcpow($this->_str, $this->_unwrap($b), 0);
		return $ret;
	}

	public function shiftRight($bits=8) {
		$ret = new self();
		$ret->_str = bcdiv($this->_str, bcpow('2', $bits));
		return $ret;
	}

	public function shiftLeft($bits=8) {
		$ret = new self();
		$ret->_str = bcmul($this->_str, bcpow('2', $bits));
		return $ret;
	}

	public function absoluteValue() {
		$ret = new self();
		if (-1 === bccomp($this->_str, '0', 0)) {
			$ret->_str = bcsub('0', $this->_str, 0);
		}
		else {
			$ret->_str = $this->_str;
		}
		return $ret;
	}
}

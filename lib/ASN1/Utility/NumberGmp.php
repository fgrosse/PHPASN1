<?php

namespace FG\ASN1\Utility;

/**
 * Class NumberGmp
 * @package FG\ASN1\Utility
 * @internal
 */
class NumberGmp extends Number
{
	/**
	 * Resource handle.
	 * @var \GMP
	 */
	protected $_rh;

	public function __clone() {
		$this->_rh = gmp_add($this->_rh, 0);
	}

	protected function _fromString($str) {
		$this->_rh = gmp_init($str, 10);
	}

	public function __toString() {
		return gmp_strval($this->_rh, 10);
	}

	public function toInteger() {
		return gmp_intval($this->_rh);
	}

	public function isNegative() {
		return gmp_sign($this->_rh) === -1;
	}

	protected function _unwrap($number) {
		if ($number instanceof self) {
			return $number->_rh;
		}
		return $number;
	}

	public function compare($number) {
		return gmp_cmp($this->_rh, $this->_unwrap($number));
	}

	public function add($b) {
		$ret = new self();
		$ret->_rh = gmp_add($this->_rh, $this->_unwrap($b));
		return $ret;
	}

	public function subtract($b) {
		$ret = new self();
		$ret->_rh = gmp_sub($this->_rh, $this->_unwrap($b));
		return $ret;
	}

	public function multiply($b) {
		$ret = new self();
		$ret->_rh = gmp_mul($this->_rh, $this->_unwrap($b));
		return $ret;
	}

	public function modulus($b) {
		$ret = new self();
		$ret->_rh = gmp_mod($this->_rh, $this->_unwrap($b));
		return $ret;
	}

	public function toPower($b) {
		if ($b instanceof self) {
			if ($b->compare(PHP_INT_MAX) > 0) {
				throw new \UnexpectedValueException('Unable to raise to power greater than PHP_INT_MAX.');
			}
			$b = gmp_intval($b->_rh);
		}
		$ret = new self();
		$ret->_rh = gmp_pow($this->_rh, $b);
		return $ret;
	}

	public function shiftRight($bits=8) {
		$ret = new self();
		$ret->_rh = gmp_div($this->_rh, gmp_pow(2, $bits));
		return $ret;
	}

	public function shiftLeft($bits=8) {
		$ret = new self();
		$ret->_rh = gmp_mul($this->_rh, gmp_pow(2, $bits));
		return $ret;
	}

	public function absoluteValue() {
		$ret = new self();
		$ret->_rh = gmp_abs($this->_rh);
		return $ret;
	}
}

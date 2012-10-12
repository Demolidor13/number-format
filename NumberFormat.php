<?php

namespace h4kuna;

use Nette\Object;

/**
 * @property-write $number
 * @property-write $thousand
 * @property-write $decimal
 * @property-write $point
 * @property-write $nbsp
 * @property-write $zeroClear
 * @property-write $mask
 * @property-write $symbol
 */
class NumberFormat extends Object {
    /** @var string utf-8 &nbsp; */

    const NBSP = "\xc2\xa0";

    /** @var string */
    private $thousand = ' ';

    /** @var int */
    private $decimal = 2;

    /** @var string */
    private $point = ',';

    /** @var bool */
    private $nbsp = TRUE;

    /** @var bool */
    private $zeroClear = FALSE;

    /** @var number */
    private $number = 0;

    /** @var string */
    private $mask = '1 S';

    /**
     * internal helper
     * @var array
     */
    private $workMask = array('', '');

    /** @var string */
    private $symbol;

    public function __construct($symbol = NULL) {
        $this->setSymbol($symbol);
    }

    public function getSymbol() {
        return $this->symbol;
    }

    public function setDecimal($val) {
        $this->decimal = $val;
        return $this;
    }

    /**
     * @example '1 S', 'S 1'
     * S = symbol
     * @param string $mask
     * @return Format
     */
    public function setMask($mask) {
        if (strpos($mask, '1') === FALSE || strpos($mask, 'S') === FALSE) {
            throw new ExchangeException('The mask consists of 1 and S.');
        }

        $this->mask = $mask;
        $this->workMask = explode('1', str_replace('S', $this->symbol, $mask));
        return $this;
    }

    public function setNumber($number) {
        $this->number = $number;
        return $this;
    }

    /**
     * @param bool $val
     * @return Format
     */
    public function setNbsp($val) {
        $this->nbsp = (bool) $val;
        return $this;
    }

    public function setPoint($val) {
        $this->point = $val;
        return $this;
    }

    public function setSymbol($symbol) {
        if ($symbol == $this->symbol) {
            return $this;
        }

        $this->symbol = $symbol;

        if ($symbol !== NULL) {
            $this->setMask($this->mask);
        }
        return $this;
    }

    public function setThousand($val) {
        $this->thousand = $val;
        return $this;
    }

    public function setZeroClear($val) {
        $this->zeroClear = (bool) $val;
        return $this;
    }

    public function toggleNbsp() {
        return $this->setNbsp(!$this->nbsp);
    }

    public function toggleZeroClear() {
        return $this->setZeroClear(!$this->zeroClear);
    }

    public function render($number = NULL, $decimal = NULL) {
        if ($number) {
            $this->setNumber($number);
        } elseif (!is_numeric($this->number)) {
            return NULL;
        }

        if ($decimal === NULL) {
            $decimal = $this->decimal;
        }

        $num = number_format($this->number, $decimal, $this->point, $this->thousand);

        if ($this->decimal > 0 && $this->zeroClear) {
            $num = rtrim(rtrim($num, '0'), $this->point);
        }

        if ($this->symbol) {
            $num = implode($num, $this->workMask);
        }

        if ($this->nbsp) {
            $num = str_replace(' ', self::NBSP, $num);
        }

        return $num;
    }

    public function __toString() {
        return $this->render();
    }

}
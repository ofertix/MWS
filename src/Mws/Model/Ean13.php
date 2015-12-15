<?php

namespace Ofertix\Mws\Model;

class Ean13
{

    protected $ean13;

    /**
     * Ean13VO constructor.
     * @param string $ean13
     */
    public function __construct($ean13)
    {
        $this->validate($ean13);
        $this->ean13 = $ean13;
    }

    /**
     * @return string
     */
    public function ean13()
    {
        return $this->ean13;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->ean13;
    }

    /**
     * @param string $ean13
     *
     * @return bool
     */
    public function validate($ean13)
    {
        if ( !preg_match('/^[0-9]{13,13}$/', $ean13) ) {
            throw new \InvalidArgumentException('Invalid EAN13 code lenght!');
        }

        $lastDigitIndex = strlen($ean13) - 1;
        $checkDigit = (int) $ean13[$lastDigitIndex];
        // reverse the actual digits (excluding the check digit)
        $str = strrev(substr($ean13, 0, $lastDigitIndex));

        /**
         *  Moving from right to left
         *  Even digits are just added
         *  Odd digits are multiplied by three
         */
        $accumulator = 0;
        for ($i = 0; $i < $lastDigitIndex; $i++) {
            $accumulator += $i % 2 ? (int) $str[$i] : (int) $str[$i] * 3;
        }
        $checksum = (10 - ($accumulator % 10)) % 10;
        if ($checksum !== $checkDigit) {
            throw new \InvalidArgumentException('Invalid EAN13 code checksum!');
        }

        return true;
    }


}

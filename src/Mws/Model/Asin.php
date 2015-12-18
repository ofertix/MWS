<?php

namespace Ofertix\Mws\Model;

class Asin
{
    protected $asin;

    /**
     * Asin constructor.
     * @param string $asin
     */
    public function __construct($asin)
    {
        $this->validate($asin);
        $this->asin = $asin;
    }

    /**
     * @return string
     */
    public function asin()
    {
        return $this->asin;
    }

    /**
     * @param string $asin
     * @return bool
     */
    public function validate($asin)
    {
        /** ToDo check correct ASIN format */
        if ( !preg_match('/^[A-Z0-9]{1,13}$/', $asin) ) {
            throw new \InvalidArgumentException('Invalid ASIN code!');
        }

        return true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->asin;
    }

}


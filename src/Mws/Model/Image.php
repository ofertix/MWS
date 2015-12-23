<?php

namespace Ofertix\Mws\Model;

/**
 * Class Image
 *
 * @package Ofertix\Mws\Model
 */
class Image
{

    protected $width;
    protected $heigth;
    protected $url;
    protected $size;

    /**
     * @param string $width
     * @param string $heigth
     * @param string $url
     */
    public function __construct($width, $heigth, $url)
    {
        $this->width = $width;
        $this->heigth = $heigth;
        $this->url = $url;
    }

    /**
     * Get Width
     *
     * @return string
     */
    public function width()
    {
        return $this->width;
    }

    /**
     * @param string $width
     *
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get Heigth
     *
     * @return string
     */
    public function heigth()
    {
        return $this->heigth;
    }

    /**
     * @param string $heigth
     *
     * @return Image
     */
    public function setHeigth($heigth)
    {
        $this->heigth = $heigth;

        return $this;
    }

    /**
     * Get Url
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get Size
     *
     * @return mixed
     */
    public function size()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     *
     * @return Image
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }


}

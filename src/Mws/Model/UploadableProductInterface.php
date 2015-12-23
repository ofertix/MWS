<?php

namespace Ofertix\Mws\Model;

interface UploadableProductInterface
{

    /**
     * @return string
     */
    public function ean13();

    /**
     * @return string
     */
    public function sku();

    /**
     * @return string
     */
    public function title();

    /**
     * @return string
     */
    public function description();

    /**
     * @return string
     */
    public function brand();

    /**
     * @return int
     */
    public function stock();

    /**
     * @return float
     */
    public function salePrice();

    /**
     * @return string[]
     */
    public function images();

}

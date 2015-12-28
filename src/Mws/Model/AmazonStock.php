<?php

namespace Ofertix\Mws\Model;

class AmazonStock implements AmazonFeedTypeInterface
{
    const FEED_NAME = 'inventory';
    use AmazonFeedTypeTrait;

    /** @var  string */
    protected $sku;
    /** @var  integer */
    protected $stock;

    /**
     * @param $sku
     * @param $stock
     */
    public function __construct($sku, $stock)
    {
        $this->validateSku($sku);
        $this->sku = $sku;

        $this->validateStock($stock);
        $this->stock = $stock;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function xmlNode()
    {
        $rootNode = new \SimpleXMLElement('<'.$this->feedName().'></'.$this->feedName().'>');
        $rootNode->addChild('SKU', $this->sku());
        $rootNode->addChild('Quantity', $this->stock());

        return $rootNode;
    }


    protected function validateSku($sku)
    {
        if ( empty($sku) ) {
            throw new \InvalidArgumentException('Invalid sku');
        }
    }

    protected function validateStock($stock)
    {
        if (!is_numeric($stock)) {
            throw new \InvalidArgumentException('Stock quantity must be integer');
        }
    }

    /**
     * Get Sku
     *
     * @return string
     */
    public function sku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     *
     * @return AmazonStock
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get Stock
     *
     * @return int
     */
    public function stock()
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     *
     * @return AmazonStock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }


}

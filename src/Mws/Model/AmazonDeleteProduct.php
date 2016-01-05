<?php

namespace Ofertix\Mws\Model;

/**
 * Class AmazonProduct
 *
 * @package Ofertix\Mws\Model
 */
class AmazonDeleteProduct implements AmazonFeedTypeInterface
{

    const FEED_NAME = 'Product';
    const FEED_TYPE = '_POST_PRODUCT_DATA_';

    /** @var  string */
    protected $sku;


    public function __construct($sku)
    {
        $this->sku = $sku;
    }

    public function feedName()
    {
        return self::FEED_NAME;
    }

    public function feedType()
    {
        return self::FEED_TYPE;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function xmlNode()
    {
        $rootNode = new \SimpleXMLElement('<'.$this->feedName().'></'.$this->feedName().'>');
        $rootNode->addChild('SKU', $this->sku());

        return $rootNode;
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
     * @return AmazonDeleteProduct
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }
}

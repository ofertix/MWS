<?php

namespace Ofertix\Mws\Model;

class AmazonPrice implements AmazonFeedTypeInterface
{
    const FEED_NAME = 'pricing';
    use AmazonFeedTypeTrait;

    /** @var  string */
    protected $sku;
    /** @var  integer */
    protected $price;
    /** @var  string */
    protected $currency;

    /**
     * @param $sku
     * @param $price
     * @param $currency
     */
    public function __construct($sku, $price, $currency)
    {
        $this->validateSku($sku);
        $this->sku = $sku;

        $this->validatePrice($price);
        $this->price = $price;

        $this->validateCurrency($currency);
        $this->currency = $currency;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function xmlNode()
    {
        $rootNode = new \SimpleXMLElement('<'.$this->feedName().'></'.$this->feedName().'>');
        $rootNode->addChild('SKU', $this->sku());
        $rootNode->addChild('StandardPrice', $this->price())
            ->addAttribute('currency', $this->currency());

        return $rootNode;
    }



    protected function validateSku($sku)
    {
        if ( empty($sku) ) {
            throw new \InvalidArgumentException('Invalid sku');
        }
    }

    protected function validatePrice($price)
    {
        if (!is_numeric($price)) {
            throw new \InvalidArgumentException('Price must be numeric');
        }
    }

    protected function validateCurrency($currency)
    {
        $validCurrencies = array('USD', 'GBP', 'EUR', 'JPY', 'CAD');
        if (! in_array($currency, $validCurrencies) ) {
            throw new \InvalidArgumentException('Invalid currency code');
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
     * @return AmazonPrice
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get Price
     *
     * @return int
     */
    public function price()
    {
        return $this->price;
    }

    /**
     * @param int $price
     *
     * @return AmazonPrice
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get Currency
     *
     * @return string
     */
    public function currency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return AmazonPrice
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

}

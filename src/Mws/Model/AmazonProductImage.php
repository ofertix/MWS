<?php

namespace Ofertix\Mws\Model;

class AmazonProductImage implements AmazonFeedTypeInterface
{
    use AmazonFeedTypeTrait;
    const FEED_NAME = 'ProductImage';

    const TYPE_MAIN = 'Main';
    const TYPE_ALTERNATE = 'PT';
    const MAX_ALTERNATES = 8;
    const TYPE_SWATCH = 'Swatch';

    /** @var  string */
    protected $sku;
    /** @var  string */
    protected $type;
    /** @var  string */
    protected $url;

    /**
     * @param $sku
     * @param $type
     * @param $url
     */
    public function __construct($sku, $type, $url)
    {
        $this->validateType($type);
        $this->type = $type;

        $this->validateSku($sku);
        $this->sku = $sku;

        $this->validateUrl($url);
        $this->url = $url;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function xmlNode()
    {
        $rootNode = new \SimpleXMLElement('<'.$this->feedName().'></'.$this->feedName().'>');
        $rootNode->addChild('SKU', $this->sku());
        $rootNode->addChild('ImageType', $this->type());
        $rootNode->addChild('ImageLocation', $this->url());

        return $rootNode;
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
     * @return array
     */
    protected function validTypes()
    {
        $validTypes[] = self::TYPE_MAIN;
        $validTypes[] = self::TYPE_SWATCH;
        $i = 1;
        while ($i <= self::MAX_ALTERNATES) {
            $validTypes[] = self::TYPE_ALTERNATE.$i;
            $i++;
        }

        return $validTypes;
    }


    protected function validateType($type)
    {
        if (! in_array($type, $this->validTypes()) ) {
            throw new \InvalidArgumentException('Invalid image type');
        }
    }

    protected function validateSku($sku)
    {
        if ( empty($sku) ) {
            throw new \InvalidArgumentException('Invalid sku');
        }
    }

    protected function validateUrl($url)
    {
        //ToDo: improve validation
        $pattern = "/^(http|https|ftp):\/\/((www.)?([A-Z0-9][A-Z0-9_-]*.)+(com|es|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)(\/[A-Z0-9][A-Z0-9._-]*)+)/i";
        if(preg_match($pattern, $url)){
            return true;
        } else{
            throw new \InvalidArgumentException('Invalid url');
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
     * @return AmazonProductImage
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return AmazonProductImage
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * @return AmazonProductImage
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

}

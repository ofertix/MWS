<?php

namespace Ofertix\Mws\Model;

/**
 * Class AmazonProduct
 */
class AmazonProduct
{
    protected $id;
    protected $ean13;
    protected $asin;
    protected $title;
    protected $color;
    protected $size;
    protected $brand;
    protected $model;
    protected $url;
    protected $price;
    protected $productGroup;
    protected $productType;
    protected $attributeId;

    /**
     * AmazonProduct constructor.
     * @param Ean13 $ean13
     * @param Asin  $asin
     * @param string  $brand
     * @param string  $model
     * @param string  $title
     */
    public function __construct(Ean13 $ean13, Asin $asin, $brand, $model, $title)
    {
        $this->ean13 = $ean13;
        $this->asin = $asin;
        $this->brand = $brand;
        $this->model = $model;
        $this->title = $title;
    }

    /**
     * Get Id
     *
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return AmazonProduct
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Ean13
     *
     * @return Ean13
     */
    public function ean13()
    {
        return $this->ean13;
    }

    /**
     * @param Ean13 $ean13
     *
     * @return AmazonProduct
     */
    public function setEan13($ean13)
    {
        $this->ean13 = $ean13;

        return $this;
    }

    /**
     * Get Asin
     *
     * @return Asin
     */
    public function asin()
    {
        return $this->asin;
    }

    /**
     * @param Asin $asin
     *
     * @return AmazonProduct
     */
    public function setAsin($asin)
    {
        $this->asin = $asin;

        return $this;
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return AmazonProduct
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Color
     *
     * @return mixed
     */
    public function color()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     *
     * @return AmazonProduct
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get ProductType
     *
     * @return mixed
     */
    public function productType()
    {
        return $this->productType;
    }

    /**
     * @param mixed $productType
     *
     * @return AmazonProduct
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;

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
     * @return AmazonProduct
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get Brand
     *
     * @return string
     */
    public function brand()
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     *
     * @return AmazonProduct
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get Model
     *
     * @return string
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * @param string $model
     *
     * @return AmazonProduct
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get Url
     *
     * @return mixed
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return AmazonProduct
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get Price
     *
     * @return mixed
     */
    public function price()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     *
     * @return AmazonProduct
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get ProductGroup
     *
     * @return mixed
     */
    public function productGroup()
    {
        return $this->productGroup;
    }

    /**
     * @param mixed $productGroup
     *
     * @return AmazonProduct
     */
    public function setProductGroup($productGroup)
    {
        $this->productGroup = $productGroup;

        return $this;
    }

    /**
     * Get AttributeId
     *
     * @return int
     */
    public function attributeId()
    {
        return $this->attributeId;
    }

    /**
     * @param $attributeId
     *
     * @return $this
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;

        return $this;
    }

}

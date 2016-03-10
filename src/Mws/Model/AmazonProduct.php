<?php

namespace Ofertix\Mws\Model;

/**
 * Class AmazonProduct
 *
 * @package Ofertix\Mws\Model
 */
class AmazonProduct implements UploadableProductInterface, AmazonFeedTypeInterface
{

    use AmazonFeedTypeTrait;
    const FEED_NAME = 'product';

    protected $id;
    /** @var Ean13  */
    protected $ean13;
    /** @var  Asin */
    protected $asin;
    protected $sku;
    protected $brand;
    protected $title;
    protected $model;
    protected $color;
    protected $size;
    protected $url;
    protected $productGroup;
    protected $productType;
    protected $stock;
    protected $salePrice;
    protected $costPrice;
    protected $description;
    protected $moreInfo;
    protected $currency;
    protected $launchDate;
    protected $category;
    protected $clothingType;
    protected $department;
    protected $nodeId;
    protected $isParent;

    /**
     * @var Image[]
     */
    protected $images;

    /**
     * AmazonProduct constructor.
     * @param Ean13 $ean13
     * @param $brand
     * @param $title
     * @param bool $parent
     */
    public function __construct(Ean13 $ean13, $brand, $title, $parent = false)
    {
        $this->ean13 = $ean13;
        $this->brand = $brand;
        $this->title = $title;
        $this->images = [];
        $this->isParent = $parent;
    }


    /**
     * @return \SimpleXMLElement|String
     */
    public function xmlNode()
    {
        $rootNode = new \SimpleXMLElement('<'.$this->feedName().'></'.$this->feedName().'>');
        $rootNode->addChild('SKU', $this->sku());
        $pid = $rootNode->addChild('StandardProductID');
        $pid->addChild('Type', 'EAN');
        $pid->addChild('Value', $this->ean13());
        if (null !== $this->launchDate()) {
            $rootNode->addChild('LaunchDate', $this->launchDate());
        }
        $conditionNode = $rootNode->addChild('Condition');
        $conditionNode->addChild('ConditionType', 'New');
        $descNode = $rootNode->addChild('DescriptionData');
        $descNode->addChild('Title', $this->xmlEscape($this->title()));
        $descNode->addChild('Brand', $this->xmlEscape($this->brand()));
        $descNode->addChild('Description', $this->xmlEscape($this->description()));

        $descNode->addChild('ItemType', 'flat-sheets');
        if (!empty($this->nodeId)) {
            $descNode->addChild('RecommendedBrowseNode', $this->nodeId);
        }
        $this->createProductDataNode($rootNode);

        return $rootNode;

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
    public function setAsin(Asin $asin)
    {
        $this->asin = $asin;

        return $this;
    }

    /**
     * Get Sku
     *
     * @return mixed
     */
    public function sku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     *
     * @return AmazonProduct
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

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
     * Get Stock
     *
     * @return mixed
     */
    public function stock()
    {
        return $this->stock;
    }

    /**
     * @param mixed $stock
     *
     * @return AmazonProduct
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get SalePrice
     *
     * @return mixed
     */
    public function salePrice()
    {
        return $this->salePrice;
    }

    /**
     * @param mixed $salePrice
     *
     * @return AmazonProduct
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    /**
     * Get CostPrice
     *
     * @return mixed
     */
    public function costPrice()
    {
        return $this->costPrice;
    }

    /**
     * @param mixed $costPrice
     *
     * @return AmazonProduct
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * Get Description
     *
     * @return mixed
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return AmazonProduct
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get MoreInfo
     *
     * @return mixed
     */
    public function moreInfo()
    {
        return $this->moreInfo;
    }

    /**
     * @param mixed $moreInfo
     *
     * @return AmazonProduct
     */
    public function setMoreInfo($moreInfo)
    {
        $this->moreInfo = $moreInfo;

        return $this;
    }

    /**
     * Get Images
     *
     * @return array
     */
    public function images()
    {
        return $this->images;
    }

    /**
     * @param Image $image
     *
     * @return $this
     */
    public function addImage(Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Get Currency
     *
     * @return mixed
     */
    public function currency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     *
     * @return AmazonProduct
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get LaunchDate
     *
     * @return mixed
     */
    public function launchDate()
    {
        return $this->launchDate;
    }

    /**
     * @param mixed $launchDate
     *
     * @return AmazonProduct
     */
    public function setLaunchDate($launchDate)
    {
        $this->launchDate = ($launchDate instanceof \DateTime) ? $launchDate->format('c') : $launchDate;

        return $this;
    }

    /**
     * Get Category
     *
     * @return mixed
     */
    public function category()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     *
     * @return AmazonProduct
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get ClothingType
     *
     * @return mixed
     */
    public function clothingType()
    {
        return $this->clothingType;
    }

    /**
     * @param mixed $clothingType
     *
     * @return AmazonProduct
     */
    public function setClothingType($clothingType)
    {
        $this->clothingType = $clothingType;

        return $this;
    }

    /**
     * @param int $nodeId
     * @return $this
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;

        return $this;
    }

    /**
     * @param $rootNode
     *
     * @return mixed
     */
    protected function createProductDataNode($rootNode)
    {

        if ($this->category() == 'Clothing') {

            return $this->createClothingNode($rootNode);
        }

        if ($this->category() == 'Shoes') {
            return $this->createShoesNode($rootNode);
        }

        if ($this->category() == 'Sports') {
            return $this->createSportsNode($rootNode);
        }

        if ($this->category() == 'ClothingAccessories') {
        }

        if ($this->category() == 'Miscellaneous') {
        }

        if ($this->category() == 'CameraPhoto') {
        }

        if ($this->category() == 'Home') {
        }

        if ($this->category() == 'SportsMemorabilia') {
        }

        if ($this->category() == 'EntertainmentCollectibles') {
        }

        if ($this->category() == 'HomeImprovement') {
        }

        if ($this->category() == 'Tools') {
        }

        if ($this->category() == 'FoodAndBeverages') {
        }

        if ($this->category() == 'Gourmet') {
        }

        if ($this->category() == 'Jewelry') {
        }

        if ($this->category() == 'Health') {
        }

        if ($this->category() == 'CE') {
        }

        if ($this->category() == 'Computers') {
        }

        if ($this->category() == 'SoftwareVideoGames') {
        }

        if ($this->category() == 'Wireless') {
        }

        if ($this->category() == 'Beauty') {
        }

        if ($this->category() == 'Office') {
        }

        if ($this->category() == 'MusicalInstruments') {
        }

        if ($this->category() == 'AutoAccessory') {
        }

        if ($this->category() == 'PetSupplies') {
        }

        if ($this->category() == 'ToysBaby') {
        }

        if ($this->category() == 'Baby') {
        }

        if ($this->category() == 'TiresAndWheels') {
        }

        if ($this->category() == 'Music') {
        }

        if ($this->category() == 'Video') {
        }

        if ($this->category() == 'Lighting') {
        }

        if ($this->category() == 'LargeAppliances') {
        }

        if ($this->category() == 'FBA') {
        }

        if ($this->category() == 'Toys') {
        }

        if ($this->category() == 'GiftCard') {
        }

        if ($this->category() == 'LabSupplies') {
        }

        if ($this->category() == 'RawMaterials') {
        }

        if ($this->category() == 'PowerTransmission') {
        }

        if ($this->category() == 'Industrial') {
        }

        if ($this->category() == 'Motorcycles') {
        }

        if ($this->category() == 'MechanicalFasteners') {
        }

        if ($this->category() == 'FoodServiceAndJanSan') {
        }

        if ($this->category() == 'WineAndAlcohol') {
        }

        if ($this->category() == 'EUCompliance') {
        }

        if ($this->category() == 'Books') {
        }

        if ($this->category() == 'AdditionalProductInformation') {
        }

        if ($this->category() == 'Arts') {
        }

        if ($this->category() == 'Luggage') {
        }

        return null;
    }

    /**
     * Ropa
     * @param \SimpleXMLElement $rootNode
     * @return \SimpleXMLElement|null
     */
    private function createClothingNode(\SimpleXMLElement $rootNode)
    {
        if ($this->department() !== null) {
            $productDataNode = $rootNode->addChild('ProductData');
            $productDataCategoryNode = $productDataNode->addChild('Clothing');
            $this->createVariationDataNode($productDataCategoryNode);

            $classificationDataNode = $productDataCategoryNode->addChild('ClassificationData');
            $classificationDataNode->addChild('ClothingType', $this->clothingType());
            $classificationDataNode->addChild('Department', $this->xmlEscape($this->department()));
            $classificationDataNode->addChild('MaterialComposition', substr(
                $this->xmlEscape($this->moreInfo()), 0, 1000
            ));
            $classificationDataNode->addChild('OuterMaterial', substr($this->xmlEscape($this->moreInfo()), 0, 500));

            return $productDataNode;
        }

        return null;
    }

    /**
     * @param \SimpleXMLElement $productDataCategoryNode
     * @return \SimpleXMLElement
     */
    private function createVariationDataNode(\SimpleXMLElement $productDataCategoryNode)
    {
        $variationDataNode = $productDataCategoryNode->addChild('VariationData');

        if ($this->isParent) {
            $variationDataNode->addChild('Parentage', 'parent');
            $variationDataNode->addChild('VariationTheme', 'Size');
        } else {
            $variationDataNode->addChild('Size', $this->xmlEscape($this->size()));
            $variationDataNode->addChild('Color', $this->xmlEscape($this->color()));
        }

        return $variationDataNode;
    }

    /**
     * Crea nodo de zapatos
     * @param \SimpleXMLElement $rootNode
     * @return mixed
     */
    protected function createShoesNode(\SimpleXMLElement $rootNode)
    {
        if ($this->department() !== null) {
            $productDataNode = $rootNode->addChild('ProductData');
            $productDataCategoryNode = $productDataNode->addChild('Shoes');

            $productDataCategoryNode->addChild('ClothingType', $this->clothingType());

            if ($this->size() || $this->color()) {
                $variationDataNode = $productDataCategoryNode->addChild('VariationData');

                if ($this->size()) {
                    $variationDataNode->addChild('Size', $this->xmlEscape($this->size()));
                }
                if ($this->color()) {
                    $variationDataNode->addChild('Color', $this->xmlEscape($this->color()));
                }
            }

            $classificationDataNode = $productDataCategoryNode->addChild('ClassificationData');
            $classificationDataNode->addChild('Department', $this->xmlEscape($this->department()));
            $classificationDataNode->addChild('MaterialComposition', substr(
                $this->xmlEscape($this->moreInfo()), 0, 500
            ));

            return $productDataNode;
        }
    }

    /**
     * Deportes
     * @param \SimpleXMLElement $rootNode
     * @return \SimpleXMLElement
     */
    private function createSportsNode(\SimpleXMLElement $rootNode)
    {
        if ($this->department() !== null) {
            $productDataNode = $rootNode->addChild('ProductData');
            $productDataCategoryNode = $productDataNode->addChild('Sports');

            $productDataCategoryNode->addChild('ProductType', $this->clothingType());

            $variationDataNode = $productDataCategoryNode->addChild('VariationData');

            if ($this->color()) {
                $variationDataNode->addChild('Color', $this->xmlEscape($this->color()));
            }

            $variationDataNode->addChild('Department', $this->xmlEscape($this->department()));

            if ($this->size()) {
                $variationDataNode->addChild('Size', $this->xmlEscape($this->size()));
            }

            $productDataCategoryNode->addChild('MaterialComposition', substr(
                $this->xmlEscape($this->moreInfo()), 0, 500
            ));

            return $productDataNode;
        }
    }

    /**
     * @return string
     */
    private function department()
    {
        return $this->department;
    }

    /**
     * The correspondent Node Path from Amazon's Browse Tree Guides (BTGs)
     * @param string $department
     * @return $this
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Escapa el XML
     * @param string $string
     * @return string
     */
    private function xmlEscape($string)
    {
        return htmlspecialchars($string, ENT_XML1);
    }
}

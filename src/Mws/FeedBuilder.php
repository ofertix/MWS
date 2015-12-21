<?php

namespace Ofertix\Mws;

use Ofertix\Mws\Model\AmazonProduct;

class FeedBuilder
{
    /** @var $rootNode \SimpleXMLElement  */
    public $rootNode;
    public $amazonProduct;

    /**
     * @param $rootNodeName
     * @param AmazonProduct $amazonProduct
     */
    public function __construct($rootNodeName, AmazonProduct $amazonProduct)
    {
        $this->rootNode = new \SimpleXMLElement('<'.$rootNodeName.'></'.$rootNodeName.'>');
        $this->amazonProduct = $amazonProduct;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function getInventoryNode()
    {
        $this->rootNode->addChild('SKU', $this->amazonProduct->sku());
        $this->rootNode->addChild('Quantity', $this->amazonProduct->stock());

        return $this->rootNode;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function getPriceNode()
    {
        $this->rootNode->addChild('SKU', $this->amazonProduct->sku());
        $this->rootNode->addChild('StandardPrice', $this->amazonProduct->price())
                        ->addAttribute('currency', $this->amazonProduct->currency());

        return $this->rootNode;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function getRelationshipNode()
    {
        $this->rootNode->addChild('SKU', $this->amazonProduct->sku());
        $this->rootNode->addChild('ParentSKU', $this->amazonProduct->parentSku());
//        foreach ($this->amazonProduct['relation'] as $relatedSku) {
//            $relationNode = $this->rootNode->addChild('Relation');
//            $relationNode->addChild('SKU', $relatedSku);
//            $relationNode->addChild('Type', 'Variation');
//        }
//
//        return $this->rootNode;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function getProductImageNode()
    {
        $this->rootNode->addChild('SKU', $this->amazonProduct->sku());
        $this->rootNode->addChild('ImageType', $this->amazonProduct['image_type']);
        $this->rootNode->addChild('ImageLocation', $this->amazonProduct['image_location']);

        return $this->rootNode;
    }

}

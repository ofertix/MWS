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

    /**
     * @return \SimpleXMLElement|String
     */
    public function getProductNode()
    {

        $this->rootNode->addChild('SKU', $this->amazonProduct->sku());
        $pid = $this->rootNode->addChild('StandardProductID');
            $pid->addChild('Type', 'EAN');
            $pid->addChild('Value', $this->amazonProduct->ean13());
        if (null !== $this->amazonProduct->launchDate()) {
            $this->rootNode->addChild('LaunchDate', $this->amazonProduct->launchDate());
        }
        $conditionNode = $this->rootNode->addChild('Condition');
            $conditionNode->addChild('ConditionType', 'New');
        $descNode = $this->rootNode->addChild('DescriptionData');
            $descNode->addChild('Title', $this->amazonProduct->title());
            $descNode->addChild('Brand', $this->amazonProduct->brand());
            $descNode->addChild('Description', $this->amazonProduct->description());
//        if (isset($this->amazonProduct['search_terms'])) {
//            foreach ($this->amazonProduct['search_terms'] as $searchTerm) {
//                $descNode->addChild('SearchTerms', $searchTerm);
//            }
//        }
        $descNode->addChild('ItemType', 'flat-sheets');
//        if (isset($this->amazonProduct['recommended_browse_node'])) {
//            $descNode->addChild('RecommendedBrowseNode', $this->amazonProduct['recommended_browse_node']);
//        }

        return $this->rootNode;
    }


}

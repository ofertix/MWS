<?php

namespace Ofertix\Mws;

class FeedBuilder
{
    /** @var $rootNode \SimpleXMLElement  */
    public $rootNode;
    public $feedProduct;

    /**
     * FeedBuilder constructor.
     * @param string $rootNodeName
     * @param array  $feedProduct
     */
    public function __construct($rootNodeName, array $feedProduct)
    {
        $this->rootNode = new \SimpleXMLElement('<'.$rootNodeName.'></'.$rootNodeName.'>');
        $this->feedProduct = $feedProduct;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function getInventoryNode()
    {
        $this->rootNode->addChild('SKU', $this->feedProduct['sku']);
        $this->rootNode->addChild('Quantity', $this->feedProduct['quantity']);

        return $this->rootNode;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function getPriceNode()
    {
        $this->rootNode->addChild('SKU', $this->feedProduct['sku']);
        $this->rootNode->addChild('StandardPrice', $this->feedProduct['standard_price'])->addAttribute('currency', 'EUR');

        return $this->rootNode;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function getRelationshipNode()
    {
        $this->rootNode->addChild('SKU', $this->feedProduct['sku']);
        $this->rootNode->addChild('ParentSKU', $this->feedProduct['parent_sku']);
        foreach ($this->feedProduct['relation'] as $relatedSku) {
            $relationNode = $this->rootNode->addChild('Relation');
            $relationNode->addChild('SKU', $relatedSku);
            $relationNode->addChild('Type', 'Variation');
        }

        return $this->rootNode;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function getProductImageNode()
    {
        $this->rootNode->addChild('SKU', $this->feedProduct['sku']);
        $this->rootNode->addChild('ImageType', $this->feedProduct['image_type']);
        $this->rootNode->addChild('ImageLocation', $this->feedProduct['image_location']);

        return $this->rootNode;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function getProductNode()
    {

        $this->rootNode->addChild('SKU', $this->feedProduct['sku']);
        $pid = $this->rootNode->addChild('StandardProductID');
            $pid->addChild('Type', 'EAN');
            $pid->addChild('Value', $this->feedProduct['ean']);
        if (isset($this->feedProduct['launch_date'])) {
            $this->rootNode->addChild('LaunchDate', $this->feedProduct['launch_date']);
        }
        $conditionNode = $this->rootNode->addChild('Condition');
            $conditionNode->addChild('ConditionType', 'New');
        $descNode = $this->rootNode->addChild('DescriptionData');
            $descNode->addChild('Title', $this->feedProduct['title']);
            $descNode->addChild('Brand', $this->feedProduct['brand']);
            $descNode->addChild('Description', $this->feedProduct['description']);
        if (isset($this->feedProduct['search_terms'])) {
            foreach ($this->feedProduct['search_terms'] as $searchTerm) {
                $descNode->addChild('SearchTerms', $searchTerm);
            }
        }
        $descNode->addChild('ItemType', 'flat-sheets');
        if (isset($this->feedProduct['recommended_browse_node'])) {
            $descNode->addChild('RecommendedBrowseNode', $this->feedProduct['recommended_browse_node']);
        }

        return $this->rootNode;
    }


}
